<?php

namespace App\Http\Controllers\Api;

use App\Enums\DiscountType;
use App\Enums\SalesItemType;
use App\Events\SkateTransaction\SkateTransactionCreateEvent;
use App\Events\SkatingAidTransaction\SkatingAidTransactionCreateEvent;
use App\Http\Common\Filter\FiltersDateRangeOrderedAt;
use App\Http\Common\Filter\FiltersLimit;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SalesOrderQueryRequest as QueryRequest;
use App\Http\Requests\CashierOrderStoreRequest as StoreRequest;
use App\Http\Requests\CashierOrderUpdateRequest as UpdateRequest;
use App\Http\Requests\CashierOrderGetRecentRequest as GetRecentRequest;
use App\Http\Requests\CashierOrderSearchRequest as SearchRequest;
use App\Http\Resources\SalesOrderResource;
use App\Models\Barcode;
use App\Models\BarcodeType;
use App\Models\Item;
use App\Models\SalesFulfillment;
use App\Models\SalesItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderMember;
use App\Models\SalesPayment;
use App\Models\SalesRestock;
use App\Models\SalesReturn;
use App\Models\SkateTransaction;
use App\Models\SkatingAidTransaction;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group SalesOrder CRUD
 */
class CashierOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * GET all
     * 
     * @authenticated
     * 
     * @return \Illuminate\Http\Response
     */
    public function index(QueryRequest $request)
    {
        $salesOrders = QueryBuilder::for(SalesOrder::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
                AllowedFilter::custom('start-between', new FiltersDateRangeOrderedAt),
                AllowedFilter::custom('limit', new FiltersLimit),
                AllowedFilter::scope('fulfilled'),
                AllowedFilter::scope('paid'),
                AllowedFilter::scope('completed'),
                'fulfillment_status',
                'payment_status'
            ])
            ->allowedIncludes(['sales_order_members', 'sales_order_members.member', 'tickets', 'location', 'customer', 'customer_address', 'sales_items', 'sales_items.item', 'sales_items.promotion', 'sales_items.promotion.preItem', 'sales_items.promotion.benefitItem', 'sales_items.student_enrollment', 'sales_fulfillments', 'sales_fulfillments.location', 'sales_returns', 'sales_returns.sales_item.item', 'sales_returns.sales_item.promotion', 'sales_returns.sales_item.promotion.preItem', 'sales_returns.sales_item.promotion.benefitItem', 'sales_returns.location', 'sales_restocks', 'sales_restocks.location', 'sales_payments'])
            ->get();
        return SalesOrderResource::collection($salesOrders);
    }

    /**
     * POST
     * 
     * Create new sales order, steps:
     * 1. create sales items
     * 2. create sales payment
     * 3. auto fulfill items
     * 4. add "change" payment if the amount paid is more than the total price
     * 5. check if any ticket to be activated
     * 6. check if any member card found
     * 
     * bodyParam:
     * {
     *   sales_payment: { ... },
     *   sales_items: [
     *     {
     *       ...
     *     }
     *   ],
     *   tickets: [
     *     {
     *       ...
     *     }
     *   ],
     *   members: [
     *     {
     *       ...
     *     }
     *   ]
     * } 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        // 1. create sales items (and skating aid transactions if bought)
        $salesOrder = SalesOrder::create([
            'ordered_at' => Carbon::now(),
            'location_id' => $request->input('location_id'),
        ]);

        if ($request->sales_items) {
            for ($index = 0; $index < count($request->sales_items); $index++) {
                $salesItem = SalesItem::create([
                    'position_index' => $request->input('sales_items.' . $index . '.position_index'),
                    'description' => $request->input('sales_items.' . $index . '.description'),
                    'qty' => $request->input('sales_items.' . $index . '.qty'),
                    'unit_price' => $request->input('sales_items.' . $index . '.unit_price'),
                    'discount_amount' => $request->input('sales_items.' . $index . '.discount_amount'),
                    'discount_type' => $request->input('sales_items.' . $index . '.discount_type'),
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $request->input('sales_items.' . $index . '.item_id'),
                    'item_type' => $request->input('sales_items.' . $index . '.item_type'),
                    'tax' => $request->input('sales_items.' . $index . '.tax'),
                    'dpp' => $request->input('sales_items.' . $index . '.dpp'),
                    'is_pb1' => $request->input('sales_items.' . $index . '.is_pb1'),
                    'pb1_tax' => $request->input('sales_items.' . $index . '.pb1_tax'),
                    'pb1_dpp' => $request->input('sales_items.' . $index . '.pb1_dpp'),
                ]);

                // if item type is student enrollment, mark it as paid                 
                if (SalesItemType::studentEnrollment()->isEqual($salesItem->item_type)) {
                    $studentEnrollment = StudentEnrollment::where('id', '=', $salesItem->item_id)->first();
                    $studentEnrollment->enrollment_status = "Paid";
                    $studentEnrollment->order_ref_no = $salesOrder->order_ref_no;
                    $studentEnrollment->save();
                }

                $itemId = $request->input('sales_items.' . $index . '.item_id');
                //if item type is skating aid, create a skating aid transaction
                if (SalesItemType::skatingAid()->isEqual($salesItem->item_type)) {
                    SkatingAidTransaction::create([
                        'sales_order_id' => $salesOrder->id,
                        'rent_start' => null,
                        'rent_end' => null,
                        'skating_aid_id' => null,
                    ]);
                }
            }
        }

        // 2. create sales payment
        $salesPayment = SalesPayment::create([
            'payment_date' => $salesOrder->ordered_at,
            'amount' => $request->input('sales_payment.amount'),
            'type' => $request->input('sales_payment.type'),
            'description' => $request->input('sales_payment.description'),
            'sales_order_id' => $salesOrder->id
        ]);

        // 3. auto fulfill items
        $this->autoFulfill($salesOrder);
        // 4. add "change" payment if the amount paid is more than the total price
        $this->autoPaymentChange($salesOrder);

        // 5. check if any ticket to be activated
        if ($request->tickets) {
            $skateTransactionArray = [];
            for ($index = 0; $index < count($request->tickets); $index++) {
                Barcode::create([
                    'barcode_id' => $request->input('tickets.' . $index . '.barcode_id'),
                    'active_on' => $request->input('tickets.' . $index . '.active_on'),
                    'session_name' => $request->input('tickets.' . $index . '.session_name'),
                    'session_day' => $request->input('tickets.' . $index . '.session_day'),
                    'session_start_time' => $request->input('tickets.' . $index . '.session_start_time'),
                    'session_end_time' => $request->input('tickets.' . $index . '.session_end_time'),
                    'sales_order_id' => $salesOrder->id,
                ]);


                $barcodePrefix = $request->input('tickets.' . $index . '.barcode_id')[0];
                $isAllowedToRentShoe = BarcodeType::where('prefix', $barcodePrefix)->first()->is_allowed_to_rent_shoe;
                if ($isAllowedToRentShoe) {
                    // also create shoe transaction once the user has pass the gate
                    $skateTransaction = new SkateTransaction();
                    $skateTransaction->barcode_id = $request->input('tickets.' . $index . '.barcode_id');
                    $skateTransaction->save();

                    array_push($skateTransactionArray, $skateTransaction);
                }
            }
            // trigger event shoe transaction
            event(new SkateTransactionCreateEvent($skateTransactionArray));
        }

        // 6. check if any member card found
        if ($request->members) {
            for ($index = 0; $index < count($request->members); $index++) {
                SalesOrderMember::create([
                    'sales_order_id' => $salesOrder->id,
                    'member_id' => $request->input('members.' . $index . '.member_id'),
                ]);
            }
        }

        // 7. create skating aid transaction
        $skatingAidTransactionArray = [];
        $salesItems = SalesItem::where('sales_order_id', $salesOrder->id)->get();
        for ($idx = 0; $idx < count($salesItems); $idx++) {
            $isExist = Item::where('id', $salesItems[$idx]->item_id)
                ->where('type', 'Skating aid')
                ->exists();
            if ($isExist) {
                $itemName = Item::where('id', $salesItems[$idx]->item_id)
                    ->first()
                    ->name;

                for ($index = 0; $index < $salesItems[$idx]->qty; $index++) {
                    $skatingAidTransaction = new SkatingAidTransaction();
                    if (strtolower(substr($itemName, 0, 7)) == 'upgrade') {
                        $skatingAidTransaction->upgraded = false;
                        $skatingAidTransaction->upgraded_name = $itemName;
                    }
                    $skatingAidTransaction->description = $itemName;
                    $skatingAidTransaction->sales_order_id = $salesOrder->id;
                    $skatingAidTransaction->sales_order_ref_no = SalesOrder::where('id', $salesOrder->id)->first()->order_ref_no;
                    $skatingAidTransaction->save();
                    array_push($skatingAidTransactionArray, $skatingAidTransaction);
                }
            }
        }
        event(new SkatingAidTransactionCreateEvent($skatingAidTransactionArray));

        // update status
        $this->refreshFulfillmentStatus($salesOrder);
        $this->refreshPaymentStatus($salesOrder);
        $salesOrder->save();

        return new SalesOrderResource($salesOrder->load(['salesOrderMembers', 'salesOrderMembers.member', 'tickets', 'location', 'customer', 'customerAddress', 'salesItems', 'salesItems.item', 'salesItems.promotion', 'salesItems.promotion.preItem', 'salesItems.promotion.benefitItem', 'salesItems.studentEnrollment', 'salesFulfillments', 'salesFulfillments.location', 'salesReturns', 'salesReturns.location', 'salesRestocks', 'salesRestocks.location', 'salesPayments']));
    }

    /**
     * GET
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = SalesOrder::withTrashed()->where('id', $id);
        $salesOrder = QueryBuilder::for($query)
            ->allowedIncludes(['sales_order_members', 'sales_order_members.member', 'tickets', 'location', 'customer', 'customer_address', 'sales_items', 'sales_items.item', 'sales_items.promotion', 'sales_items.promotion.preItem', 'sales_items.promotion.benefitItem', 'sales_items.student_enrollment', 'sales_fulfillments', 'sales_fulfillments.location', 'sales_returns', 'sales_returns.location', 'sales_returns.sales_item.item', 'sales_returns.sales_item.promotion', 'sales_returns.sales_item.promotion.preItem', 'sales_returns.sales_item.promotion.benefitItem', 'sales_restocks', 'sales_restocks.location', 'sales_payments'])
            ->firstOrFail();
        return new SalesOrderResource($salesOrder);
    }

    /**
     * PUT
     * 
     * Edit Sales order, only permit to:
     * 1. Refund item (sales item)
     * 2. change ticket session
     * 3. delete ticket session (if the item refunded)
     * 4. refunded amount
     * 
     * Edited sales order will also effect the payment
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, $salesOrderId)
    {
        $salesOrder = SalesOrder::where('id', '=', $salesOrderId)->firstOrFail();

        $now = Carbon::now();
        if ($request->sales_returns) {
            for ($index = 0; $index < count($request->sales_returns); $index++) {
                $salesReturn = SalesReturn::create([
                    'description' => $request->input('sales_returns.' . $index . '.description'),
                    'qty' => $request->input('sales_returns.' . $index . '.qty'),
                    'returned_date' => $now,
                    'discard_stock' => false,
                    'sales_order_id' => $salesOrder->id,
                    'sales_item_id' => $request->input('sales_returns.' . $index . '.sales_item_id'),
                    'location_id' => $request->input('sales_returns.' . $index . '.location_id'),
                ]);

                // if item type is student enrollment, mark it as refunded                 
                if (SalesItemType::studentEnrollment()->isEqual($salesReturn->salesItem->item_type)) {
                    $studentEnrollment = StudentEnrollment::where('id', '=', $salesReturn->salesItem->item_id)->first();
                    $studentEnrollment->enrollment_status = "Refunded";
                    $studentEnrollment->save();
                }

                //get the tax per item
                $salesItem = SalesItem::where('id', '=', $salesReturn->sales_item_id)->firstOrFail();
                $taxTotal = $salesItem->tax;
                $qtyTotal = $salesItem->qty;
                $taxPerItem = $taxTotal / $qtyTotal;

                //get the tax back from the refunded items
                $refundedTax = -($taxPerItem * $salesReturn->qty);

                //set the sales return's tax_amount
                $salesReturn->tax_amount = $refundedTax;
                $salesReturn->save();
            }
        }

        $tickets = [];
        if ($request->tickets) {
            $skateTransactionArray = [];

            for ($index = 0; $index < count($request->tickets); $index++) {
                // check for skate transaction
                $ticket = Barcode::where('id', '=', $request->input('tickets.' . $index . '.id'))->first();
                if ($ticket) {
                    // if existing ticket found, update skate transaction
                    $skateTransaction = SkateTransaction::where('barcode_id', '=', $ticket->barcode_id)->first();
                    if ($skateTransaction) {
                        $skateTransaction->barcode_id = $request->input('tickets.' . $index . '.barcode_id');
                        $skateTransaction->save();

                        array_push($skateTransactionArray, $skateTransaction);
                    }

                    // update ticket
                    $ticket->barcode_id = $request->input('tickets.' . $index . '.barcode_id');
                    $ticket->active_on = $request->input('tickets.' . $index . '.active_on');
                    $ticket->session_name = $request->input('tickets.' . $index . '.session_name');
                    $ticket->session_day = $request->input('tickets.' . $index . '.session_day');
                    $ticket->session_start_time = $request->input('tickets.' . $index . '.session_start_time');
                    $ticket->session_end_time = $request->input('tickets.' . $index . '.session_end_time');
                    $ticket->sales_order_id = $salesOrder->id;
                    $ticket->save();

                    array_push($tickets, $ticket->id);
                } else {
                    // create new ticket
                    $ticket = new Barcode;
                    $ticket->barcode_id = $request->input('tickets.' . $index . '.barcode_id');
                    $ticket->active_on = $request->input('tickets.' . $index . '.active_on');
                    $ticket->session_name = $request->input('tickets.' . $index . '.session_name');
                    $ticket->session_day = $request->input('tickets.' . $index . '.session_day');
                    $ticket->session_start_time = $request->input('tickets.' . $index . '.session_start_time');
                    $ticket->session_end_time = $request->input('tickets.' . $index . '.session_end_time');
                    $ticket->sales_order_id = $salesOrder->id;
                    $ticket->save();

                    array_push($tickets, $ticket->id);

                    // create new skate transaction
                    $barcodePrefix = $ticket->barcode_id[0];
                    $isAllowedToRentShoe = BarcodeType::where('prefix', $barcodePrefix)->first()->is_allowed_to_rent_shoe;
                    if ($isAllowedToRentShoe) {
                        // also create shoe transaction once the user has pass the gate
                        $skateTransaction = new SkateTransaction();
                        $skateTransaction->barcode_id = $ticket->barcode_id;
                        $skateTransaction->save();

                        array_push($skateTransactionArray, $skateTransaction);
                    }
                }
            }

            // trigger event shoe transaction
            event(new SkateTransactionCreateEvent($skateTransactionArray));
        }
        // Remove one by one to make sure observer is called
        $tempTickets = $salesOrder->tickets()->whereNotIn('id', $tickets)->get();
        foreach ($tempTickets as $tempTicket) {
            SkateTransaction::where('barcode_id', '=', $tempTicket->barcode_id)->delete();
            $tempTicket->delete();
        }

        $salesPayment = SalesPayment::create([
            'payment_date' => $now,
            'amount' => $request->input('sales_payment.amount'),
            'type' => 'Refund',
            'description' => 'Refund',
            'sales_order_id' => $salesOrder->id
        ]);

        // update status
        $this->refreshPaymentStatus($salesOrder);
        $salesOrder->save();

        return new SalesOrderResource($salesOrder->load(['salesOrderMembers', 'salesOrderMembers.member', 'tickets', 'location', 'customer', 'customerAddress', 'salesItems', 'salesItems.item', 'salesItems.promotion', 'salesItems.promotion.preItem', 'salesItems.promotion.benefitItem', 'salesItems.studentEnrollment', 'salesFulfillments', 'salesFulfillments.location', 'salesReturns', 'salesReturns.location', 'salesReturns.salesItem.item', 'salesReturns.salesItem.promotion', 'salesReturns.salesItem.promotion.preItem', 'salesReturns.salesItem.promotion.benefitItem', 'salesReturns.salesItem.studentEnrollment', 'salesRestocks', 'salesRestocks.location', 'salesPayments']));
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $salesOrderId)
    {
        $salesOrder = SalesOrder::where('id', '=', $salesOrderId)->firstOrFail();
        $salesOrder->delete();

        return response()->json(null, 204);
    }

    /**
     * Restore
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, int $id)
    {
        $salesOrder = SalesOrder::withTrashed()->findOrFail($id);
        if ($salesOrder->trashed()) $salesOrder->restore();

        return new SalesOrderResource($salesOrder);
    }

    /**
     * Ticket barcode and sales order id can be used to search order
     */
    public function searchOrder(SearchRequest $request, $id)
    {
        // check if the id given is order_ref_no, if not found, proceed to search by ticket 
        $salesOrder = SalesOrder::where('order_ref_no', '=', $id)->first();
        if ($salesOrder) {
            return new SalesOrderResource($salesOrder->load(['salesOrderMembers', 'salesOrderMembers.member', 'tickets', 'location', 'customer', 'customerAddress', 'salesItems', 'salesItems.item', 'salesItems.promotion', 'salesItems.promotion.preItem', 'salesItems.promotion.benefitItem', 'salesItems.studentEnrollment', 'salesFulfillments', 'salesFulfillments.location', 'salesReturns', 'salesReturns.salesItem.item', 'salesReturns.salesItem.promotion', 'salesReturns.salesItem.promotion.preItem', 'salesReturns.salesItem.promotion.benefitItem', 'salesReturns.salesItem.studentEnrollment', 'salesReturns.location', 'salesRestocks', 'salesRestocks.location', 'salesPayments']));
        }

        // check if the id given is ticket barcode, if not found, return 404
        $salesOrder = SalesOrder::join('barcodes', function ($join) use ($id) {
            $join->on('barcodes.sales_order_id', '=', 'sales_orders.id')
                ->where('barcodes.barcode_id', '=', $id);
        })->select('sales_orders.*')->firstOrFail();

        if ($salesOrder) {
            return new SalesOrderResource($salesOrder->load(['salesOrderMembers', 'salesOrderMembers.member', 'tickets', 'location', 'customer', 'customerAddress', 'salesItems', 'salesItems.item', 'salesItems.promotion', 'salesItems.promotion.preItem', 'salesItems.promotion.benefitItem', 'salesItems.studentEnrollment', 'salesFulfillments', 'salesFulfillments.location', 'salesReturns', 'salesReturns.salesItem.item', 'salesReturns.salesItem.promotion', 'salesReturns.salesItem.promotion.preItem', 'salesReturns.salesItem.promotion.benefitItem', 'salesReturns.salesItem.studentEnrollment', 'salesReturns.location', 'salesRestocks', 'salesRestocks.location', 'salesPayments']));
        }

        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }

    public function getRecentOrder(GetRecentRequest $request, $amount = 5)
    {
        $salesOrders = SalesOrder::orderBy('created_at', 'desc')
            ->limit($amount)
            ->get();
        return SalesOrderResource::collection($salesOrders);
    }

    /**
     * Progress:
     * 
     *     0%        n%       100%     >100%
     *  Un-paid    On-going   Paid   Over-paid       
     */
    private function refreshPaymentStatus(SalesOrder $salesOrder)
    {
        $totalPrice = 0;
        foreach ($salesOrder->salesItems as $salesItem) {
            $totalPrice += $salesItem->getSubTotal();
        }

        $totalPayment = 0;
        foreach ($salesOrder->salesPayments as $salesPayment) {
            $totalPayment += $salesPayment->amount;
        }

        if ($totalPayment > $totalPrice) {
            $salesOrder->payment_status = "Overpaid";
        } else if ($totalPayment == $totalPrice) {
            $salesOrder->payment_status = "Paid";
        } else if ($totalPayment < $totalPrice && $totalPayment > 0) {
            $salesOrder->payment_status = "On-going payment";
        } else {
            $salesOrder->payment_status = "Unpaid";
        }
    }

    /**
     * status : Unfulfilled, Fulfilled and Over-fulfilled
     * 
     * only fulfilled if
     * sales item qty = fulfillment qty & sales return qty = sales restock qty
     * 
     * over fulfilled if
     * sales item qty + sales return qty < sales fulfillment qty + sales restock qty
     */
    private function refreshFulfillmentStatus(SalesOrder $salesOrder)
    {
        $salesItemQty = 0;
        foreach ($salesOrder->salesItems as $salesItem) {
            $salesItemQty += $salesItem->qty;
        }

        $salesFulfillmentQty = 0;
        foreach ($salesOrder->salesFulfillments as $salesFulfillment) {
            $salesFulfillmentQty += $salesFulfillment->qty;
        }

        $salesReturnQty = 0;
        foreach ($salesOrder->salesReturns as $salesReturn) {
            $salesReturnQty += $salesReturn->qty;
        }

        $salesRestockQty = 0;
        foreach ($salesOrder->salesRestocks as $salesRestock) {
            $salesRestockQty += $salesRestock->qty;
        }

        if (($salesItemQty == $salesFulfillmentQty) && ($salesReturnQty == $salesRestockQty)) {
            $salesOrder->fulfillment_status = "Fulfilled";
        } else if (($salesItemQty + $salesReturnQty) < ($salesFulfillmentQty + $salesRestockQty)) {
            $salesOrder->fulfillment_status = "Overfulfilled";
        } else if (($salesFulfillmentQty > 0) || ($salesReturnQty) || ($salesRestockQty)) {
            $salesOrder->fulfillment_status = "On-going fulfilment";
        } else {
            $salesOrder->fulfillment_status = "Unfulfilled";
        }
    }

    /**
     * fulfill all sales item base on the given sales order
     */
    private function autoFulfill(SalesOrder $salesOrder)
    {
        $now = Carbon::now();
        foreach ($salesOrder->salesItems as $salesItem) {
            SalesFulfillment::create([
                'qty' => $salesItem->qty,
                'fulfilled_date' => $now,
                'sales_order_id' => $salesOrder->id,
                'sales_item_id' => $salesItem->id,
                'location_id' => $salesOrder->location->id,
            ]);
        }
    }

    /**
     * auto calculate the change if the sum amount of money given is more than the total price
     * if payment is less than money given, no change created
     */
    private function autoPaymentChange(SalesOrder $salesOrder)
    {
        $totalPrice = 0;
        foreach ($salesOrder->salesItems as $salesItem) {
            $totalPrice += $salesItem->getSubTotal();
        }

        $totalPayment = 0;
        foreach ($salesOrder->salesPayments as $salesPayment) {
            $totalPayment += $salesPayment->amount;
        }

        if ($totalPrice < $totalPayment) {
            SalesPayment::create([
                'description' => '',
                'payment_date' => Carbon::now(),
                'amount' => $totalPrice - $totalPayment,
                'type' => 'Change',
                'sales_order_id' => $salesOrder->id,
            ]);
        }
    }
}

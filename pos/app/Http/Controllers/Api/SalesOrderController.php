<?php

namespace App\Http\Controllers\Api;

use App\Enums\DiscountType;
use App\Http\Common\Filter\FiltersDateRangeOrderedAt;
use App\Http\Common\Filter\FiltersLimit;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\SalesOrderQueryRequest as QueryRequest;
use App\Http\Requests\SalesOrderStoreRequest as StoreRequest;
use App\Http\Requests\SalesOrderUpdateRequest as UpdateRequest;
use App\Http\Resources\SalesOrderResource;
use App\Models\SalesFulfillment;
use App\Models\SalesItem;
use App\Models\SalesOrder;
use App\Models\SalesPayment;
use App\Models\SalesRestock;
use App\Models\SalesReturn;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group SalesOrder CRUD
 */
class SalesOrderController extends Controller
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
            ->allowedIncludes(['location', 'customer', 'customer_address', 'sales_items', 'sales_items.item', 'sales_fulfillments', 'sales_fulfillments.location', 'sales_returns', 'sales_returns.location', 'sales_restocks', 'sales_restocks.location', 'sales_payments'])
            ->get();
        return SalesOrderResource::collection($salesOrders);
    }

    /**
     * POST
     * 
     * Create new sales order with the sales items 
     * 
     * bodyParam:
     * {
     *   name: "value",
     *   ordered_at: "value",
     *   fulfillment_status: 0,
     *   payment_status: "value",
     *   cancelled_at: "value",
     *   cancel_reason: "value",
     *   customer_id: "value",
     *   sales_items: [
     *     {
     *       name: "value",
     *       description: "value",
     *       qty: "value",
     *       unit_price: "value",
     *       discount_amount: "value",
     *       discount_type: "value",
     *       sales_order_id: "value",
     *       item_id: "value",
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
        $salesOrder = SalesOrder::create([
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'fulfillment_remark' => $request->input('fulfillment_remark'),
            'return_remark' => $request->input('return_remark'),
            'restock_remark' => $request->input('restock_remark'),
            'payment_remark' => $request->input('payment_remark'),
            'cancel_reason' => $request->input('cancel_reason'),
            'customer_id' => $request->input('customer_id'),
            'customer_address_id' => $request->input('customer_address_id'),
            'location_id' => $request->input('location_id'),
        ]);

        if ($request->sales_items) {
            for ($index = 0; $index < count($request->sales_items); $index++) {
                SalesItem::create([
                    'position_index' => $request->input('sales_items.' . $index . '.position_index'),
                    'description' => $request->input('sales_items.' . $index . '.description'),
                    'qty' => $request->input('sales_items.' . $index . '.qty'),
                    'tax' => $request->input('sales_items.' . $index . '.tax'),
                    'dpp' => $request->input('sales_items.' . $index . '.dpp'),
                    'is_pb1' => $request->input('sales_items.' . $index . '.is_pb1'),
                    'pb1_tax' => $request->input('sales_items.' . $index . '.pb1_tax'),
                    'pb1_dpp' => $request->input('sales_items.' . $index . '.pb1_dpp'),
                    'unit_price' => $request->input('sales_items.' . $index . '.unit_price'),
                    'discount_amount' => $request->input('sales_items.' . $index . '.discount_amount'),
                    'discount_type' => $request->input('sales_items.' . $index . '.discount_type'),
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $request->input('sales_items.' . $index . '.item_id'),
                ]);
            }
        }

        // update status
        $this->refreshFulfillmentStatus($salesOrder);
        $this->refreshPaymentStatus($salesOrder);
        $salesOrder->save();

        return new SalesOrderResource($salesOrder->load(['location', 'customer', 'customerAddress', 'salesItems', 'salesItems.item', 'salesFulfillments', 'salesFulfillments.location', 'salesReturns', 'salesReturns.location', 'salesRestocks', 'salesRestocks.location', 'salesPayments']));
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
            ->allowedIncludes(['location', 'customer', 'customer_address', 'sales_items', 'sales_items.item', 'sales_fulfillments', 'sales_fulfillments.location', 'sales_returns', 'sales_returns.location', 'sales_restocks', 'sales_restocks.location', 'sales_payments'])
            ->firstOrFail();
        return new SalesOrderResource($salesOrder);
    }

    /**
     * PUT
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, SalesOrder $salesOrder)
    {
        $salesOrder->update([
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'fulfillment_remark' => $request->input('fulfillment_remark'),
            'return_remark' => $request->input('return_remark'),
            'restock_remark' => $request->input('restock_remark'),
            'payment_remark' => $request->input('payment_remark'),
            'cancel_reason' => $request->input('cancel_reason'),
            'customer_id' => $request->input('customer_id'),
            'customer_address_id' => $request->input('customer_address_id'),
            'location_id' => $request->input('location_id'),
        ]);

        $salesItems = [];
        if ($request->sales_items) {
            for ($index = 0; $index < count($request->sales_items); $index++) {
                $salesItem = SalesItem::updateOrCreate([
                    'id' => $request->input('sales_items.' . $index . '.id'),
                ], [
                    'position_index' => $request->input('sales_items.' . $index . '.position_index'),
                    'description' => $request->input('sales_items.' . $index . '.description'),
                    'qty' => $request->input('sales_items.' . $index . '.qty'),
                    'tax' => $request->input('sales_items.' . $index . '.tax'),
                    'dpp' => $request->input('sales_items.' . $index . '.dpp'),
                    'unit_price' => $request->input('sales_items.' . $index . '.unit_price'),
                    'discount_amount' => $request->input('sales_items.' . $index . '.discount_amount'),
                    'discount_type' => $request->input('sales_items.' . $index . '.discount_type'),
                    'sales_order_id' => $salesOrder->id,
                    'item_id' => $request->input('sales_items.' . $index . '.item_id'),
                ]);

                array_push($salesItems, $salesItem->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesItems = $salesOrder->salesItems()->whereNotIn('id', $salesItems)->get();
        foreach ($tempSalesItems as $tempSalesItem) $tempSalesItem->delete();

        $salesFulfillments = [];
        if ($request->sales_fulfillments) {
            for ($index = 0; $index < count($request->sales_fulfillments); $index++) {
                $salesFulfillment = SalesFulfillment::updateOrCreate([
                    'id' => $request->input('sales_fulfillments.' . $index . '.id'),
                ], [
                    'description' => $request->input('sales_fulfillments.' . $index . '.description'),
                    'qty' => $request->input('sales_fulfillments.' . $index . '.qty'),
                    'fulfilled_date' => $request->input('sales_fulfillments.' . $index . '.fulfilled_date'),
                    'location_id' => $request->input('sales_fulfillments.' . $index . '.location_id'),
                    'sales_order_id' => $salesOrder->id,
                    'sales_item_id' => $request->input('sales_fulfillments.' . $index . '.sales_item_id'),
                ]);

                array_push($salesFulfillments, $salesFulfillment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesFulfillments = $salesOrder->salesFulfillments()->whereNotIn('id', $salesFulfillments)->get();
        foreach ($tempSalesFulfillments as $tempSalesFulfillment) $tempSalesFulfillment->delete();

        $salesReturns = [];
        if ($request->sales_returns) {
            for ($index = 0; $index < count($request->sales_returns); $index++) {
                $salesReturn = SalesReturn::updateOrCreate([
                    'id' => $request->input('sales_returns.' . $index . '.id'),
                ], [
                    'description' => $request->input('sales_returns.' . $index . '.description'),
                    'qty' => $request->input('sales_returns.' . $index . '.qty'),
                    'tax_amount' => 0,
                    'returned_date' => $request->input('sales_returns.' . $index . '.returned_date'),
                    'discard_stock' => $request->input('sales_returns.' . $index . '.discard_stock'),
                    'sales_order_id' => $salesOrder->id,
                    'sales_item_id' => $request->input('sales_returns.' . $index . '.sales_item_id'),
                    'location_id' => $request->input('sales_returns.' . $index . '.location_id'),
                ]);

                //get the tax per item
                $salesItem = SalesItem::where('id','=',$salesReturn->sales_item_id)->firstOrFail();
                $taxTotal = $salesItem->tax;
                $qtyTotal = $salesItem->qty;
                $taxPerItem = $taxTotal / $qtyTotal;

                //get the tax back from the refunded items
                $refundedTax = -($taxPerItem * $salesReturn->qty);

                //set the sales return's tax_amount
                $salesReturn->tax_amount = $refundedTax;
                $salesReturn->save();

                array_push($salesReturns, $salesReturn->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesReturns = $salesOrder->salesReturns()->whereNotIn('id', $salesReturns)->get();
        foreach ($tempSalesReturns as $tempSalesReturn) $tempSalesReturn->delete();

        $salesRestocks = [];
        if ($request->sales_restocks) {
            for ($index = 0; $index < count($request->sales_restocks); $index++) {
                $saleRestock = SalesRestock::updateOrCreate([
                    'id' => $request->input('sales_restocks.' . $index . '.id'),
                ], [
                    'description' => $request->input('sales_restocks.' . $index . '.description'),
                    'qty' => $request->input('sales_restocks.' . $index . '.qty'),
                    'restocked_date' => $request->input('sales_restocks.' . $index . '.restocked_date'),
                    'sales_order_id' => $salesOrder->id,
                    'sales_item_id' => $request->input('sales_restocks.' . $index . '.sales_item_id'),
                    'location_id' => $request->input('sales_restocks.' . $index . '.location_id'),
                ]);

                array_push($salesRestocks, $saleRestock->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesRestocks = $salesOrder->salesRestocks()->whereNotIn('id', $salesRestocks)->get();
        foreach ($tempSalesRestocks as $tempSalesRestock) $tempSalesRestock->delete();

        $salesPayments = [];
        if ($request->sales_payments) {
            for ($index = 0; $index < count($request->sales_payments); $index++) {
                $salesPayment = SalesPayment::updateOrCreate([
                    'id' => $request->input('sales_payments.' . $index . '.id'),
                ], [
                    'description' => $request->input('sales_payments.' . $index . '.description'),
                    'payment_date' => $request->input('sales_payments.' . $index . '.payment_date'),
                    'amount' => $request->input('sales_payments.' . $index . '.amount'),
                    'type' => $request->input('sales_payments.' . $index . '.type'),
                    'sales_order_id' => $salesOrder->id,
                ]);

                array_push($salesPayments, $salesPayment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesPayments = $salesOrder->salesPayments()->whereNotIn('id', $salesPayments)->get();
        foreach ($tempSalesPayments as $tempSalesPayment) $tempSalesPayment->delete();

        // update status
        $this->refreshFulfillmentStatus($salesOrder);
        $this->refreshPaymentStatus($salesOrder);
        $salesOrder->save();

        return new SalesOrderResource($salesOrder->load(['location', 'customer', 'customerAddress', 'salesItems', 'salesItems.item', 'salesFulfillments', 'salesFulfillments.location', 'salesReturns', 'salesReturns.location', 'salesRestocks', 'salesRestocks.location', 'salesPayments']));
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SalesOrder $salesOrder)
    {
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
     * GET discount types
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getDiscountTypes(Request $request)
    {
        return response()->json(['data' => DiscountType::getValues()], 200);
    }

    public function fulfill(Request $request, $salesOrderId)
    {
        $salesOrder = SalesOrder::findOrFail($salesOrderId);

        $salesFulfillments = [];
        if ($request->sales_fulfillments) {
            for ($index = 0; $index < count($request->sales_fulfillments); $index++) {
                $salesFulfillment = SalesFulfillment::updateOrCreate([
                    'id' => $request->input('sales_fulfillments.' . $index . '.id'),
                ], [
                    'description' => $request->input('sales_fulfillments.' . $index . '.description'),
                    'qty' => $request->input('sales_fulfillments.' . $index . '.qty'),
                    'fulfilled_date' => $request->input('sales_fulfillments.' . $index . '.fulfilled_date'),
                    'location_id' => $request->input('sales_fulfillments.' . $index . '.location_id'),
                    'sales_order_id' => $salesOrder->id,
                    'sales_item_id' => $request->input('sales_fulfillments.' . $index . '.sales_item_id'),
                ]);

                array_push($salesFulfillments, $salesFulfillment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesFulfillments = $salesOrder->salesFulfillments()->whereNotIn('id', $salesFulfillments)->get();
        foreach ($tempSalesFulfillments as $tempSalesFulfillment) $tempSalesFulfillment->delete();

        $salesReturns = [];
        if ($request->sales_returns) {
            for ($index = 0; $index < count($request->sales_returns); $index++) {
                $salesReturn = SalesReturn::updateOrCreate([
                    'id' => $request->input('sales_returns.' . $index . '.id'),
                ], [
                    'description' => $request->input('sales_returns.' . $index . '.description'),
                    'qty' => $request->input('sales_returns.' . $index . '.qty'),
                    'returned_date' => $request->input('sales_returns.' . $index . '.returned_date'),
                    'discard_stock' => $request->input('sales_returns.' . $index . '.discard_stock'),
                    'sales_order_id' => $salesOrder->id,
                    'sales_item_id' => $request->input('sales_returns.' . $index . '.sales_item_id'),
                    'location_id' => $request->input('sales_returns.' . $index . '.location_id'),
                ]);

                array_push($salesReturns, $salesReturn->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesReturns = $salesOrder->salesReturns()->whereNotIn('id', $salesReturns)->get();
        foreach ($tempSalesReturns as $tempSalesReturn) $tempSalesReturn->delete();

        $salesRestocks = [];
        if ($request->sales_restocks) {
            for ($index = 0; $index < count($request->sales_restocks); $index++) {
                $saleRestock = SalesRestock::updateOrCreate([
                    'id' => $request->input('sales_restocks.' . $index . '.id'),
                ], [
                    'description' => $request->input('sales_restocks.' . $index . '.description'),
                    'qty' => $request->input('sales_restocks.' . $index . '.qty'),
                    'restocked_date' => $request->input('sales_restocks.' . $index . '.restocked_date'),
                    'sales_order_id' => $salesOrder->id,
                    'sales_item_id' => $request->input('sales_restocks.' . $index . '.sales_item_id'),
                    'location_id' => $request->input('sales_restocks.' . $index . '.location_id'),
                ]);

                array_push($salesRestocks, $saleRestock->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesRestocks = $salesOrder->salesRestocks()->whereNotIn('id', $salesRestocks)->get();
        foreach ($tempSalesRestocks as $tempSalesRestock) $tempSalesRestock->delete();

        // update status
        $this->refreshFulfillmentStatus($salesOrder);
        $salesOrder->save();

        return new SalesOrderResource($salesOrder->load(['location', 'customer', 'customerAddress', 'salesItems', 'salesItems.item', 'salesFulfillments', 'salesFulfillments.location', 'salesReturns', 'salesReturns.location', 'salesRestocks', 'salesRestocks.location', 'salesPayments']));
    }

    public function pay(Request $request, $salesOrderId)
    {
        $salesOrder = SalesOrder::findOrFail($salesOrderId);

        $salesPayments = [];
        if ($request->sales_payments) {
            for ($index = 0; $index < count($request->sales_payments); $index++) {
                $salesPayment = SalesPayment::updateOrCreate([
                    'id' => $request->input('sales_payments.' . $index . '.id'),
                ], [
                    'description' => $request->input('sales_payments.' . $index . '.description'),
                    'payment_date' => $request->input('sales_payments.' . $index . '.payment_date'),
                    'amount' => $request->input('sales_payments.' . $index . '.amount'),
                    'type' => $request->input('sales_payments.' . $index . '.type'),
                    'sales_order_id' => $salesOrder->id,
                ]);

                array_push($salesPayments, $salesPayment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSalesPayments = $salesOrder->salesPayments()->whereNotIn('id', $salesPayments)->get();
        foreach ($tempSalesPayments as $tempSalesPayment) $tempSalesPayment->delete();

        // update status
        $this->refreshPaymentStatus($salesOrder);
        $salesOrder->save();

        return new SalesOrderResource($salesOrder->load(['location', 'customer', 'customerAddress', 'salesItems', 'salesItems.item', 'salesFulfillments', 'salesFulfillments.location', 'salesReturns', 'salesReturns.location', 'salesRestocks', 'salesRestocks.location', 'salesPayments']));
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
}

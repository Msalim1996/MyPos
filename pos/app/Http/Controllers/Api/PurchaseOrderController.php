<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersDateRangeOrderedAt;
use App\Http\Common\Filter\FiltersLimit;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseOrderQueryRequest as QueryRequest;
use App\Http\Requests\PurchaseOrderStoreRequest as StoreRequest;
use App\Http\Requests\PurchaseOrderUpdateRequest as UpdateRequest;
use App\Http\Resources\PurchaseOrderResource;
use App\Models\PurchaseFulfillment;
use App\Models\PurchaseItem;
use App\Models\PurchaseOrder;
use App\Models\PurchasePayment;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PurchaseOrderController extends Controller
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
    public function index()
    {
        $purchaseOrders = QueryBuilder::for(PurchaseOrder::class)
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
            ->allowedIncludes(['location', 'supplier', 'purchase_fulfillments', 'purchase_items', 'supplier_address', 'purchase_items.item', 'purchase_fulfillments.location', 'purchase_payments','purchase_fulfillments.purchase_item.item'])
            ->get();
        return PurchaseOrderResource::collection($purchaseOrders);
    }

    /**
     * POST
     * 
     * Create new purchase order with the purchase items 
     * 
     * bodyParam:
     * {
     *   supplier_id: "value",
     *   location_id: 0,
     *   fulfillment_status: "value",
     *   payment_status: "value",
     *   deadline: "value",
     *   purchase_items: [
     *     {
     *       purchase_order_id: "value",
     *       item_id: "value",
     *       qty: "value",
     *       unit_price: "value",
     *       description: "value",
     *       position_index : "value",
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
        $purchaseOrder = PurchaseOrder::create([
            'purchase_order_ref_no' => $request->input('purchase_order_ref_no'),
            'supplier_id' => $request->input('supplier_id'),
            'supplier_address_id' => $request->input('supplier_address_id'),
            'location_id' => $request->input('location_id'),
            'fulfillment_status' => $request->input('fulfillment_status'),
            'payment_status' => $request->input('payment_status'),
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'fulfillment_remark' => $request->input('fulfillment_remark'),
            'payment_remark' => $request->input('payment_remark'),
        ]);

        if ($request->purchase_items) {
            for ($index = 0; $index < count($request->purchase_items); $index++) {
                PurchaseItem::create([
                    'position_index' => $request->input('purchase_items.' . $index . '.position_index'),
                    'qty' => $request->input('purchase_items.' . $index . '.qty'),
                    'unit_price' => $request->input('purchase_items.' . $index . '.unit_price'),
                    'description' => $request->input('purchase_items.' . $index . '.description'),
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $request->input('purchase_items.' . $index . '.item_id')
                ]);
            }
        }

        // update status
        $this->refreshFulfillmentStatus($purchaseOrder);
        $this->refreshPaymentStatus($purchaseOrder);
        $purchaseOrder->save();

        return new PurchaseOrderResource($purchaseOrder->load(['location', 'supplier', 'purchaseFulfillments', 'purchaseItems', 'supplierAddress', 'purchaseItems.item', 'purchaseFulfillments.location', 'purchasePayments','purchaseFulfillments.purchaseItem.item']));
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
        $query = PurchaseOrder::withTrashed()->where('id', $id);
        $purchaseOrder = QueryBuilder::for($query)
            ->allowedIncludes(['location', 'supplier', 'purchase_fulfillments', 'purchase_items', 'purchase_items.item', 'supplier_address', 'purchase_fulfillments.location', 'purchase_payments','purchase_fulfillments.purchase_item.item'])
            ->firstOrFail();
        return new PurchaseOrderResource($purchaseOrder);
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
    public function update(UpdateRequest $request, PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update([
            'purchase_order_ref_no' => $request->input('purchase_order_ref_no'),
            'supplier_id' => $request->input('supplier_id'),
            'supplier_address_id' => $request->input('supplier_address_id'),
            'location_id' => $request->input('location_id'),
            'fulfillment_status' => $request->input('fulfillment_status'),
            'payment_status' => $request->input('payment_status'),
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'fulfillment_remark' => $request->input('fulfillment_remark'),
            'payment_remark' => $request->input('payment_remark'),
        ]);

        $purchaseItems = [];
        if ($request->purchase_items) {
            for ($index = 0; $index < count($request->purchase_items); $index++) {
                $purchaseItem = PurchaseItem::updateOrCreate([
                    'id' => $request->input('purchase_items.' . $index . '.id'),
                ], [
                    'position_index' => $request->input('purchase_items.' . $index . '.position_index'),
                    'qty' => $request->input('purchase_items.' . $index . '.qty'),
                    'unit_price' => $request->input('purchase_items.' . $index . '.unit_price'),
                    'description' => $request->input('purchase_items.' . $index . '.description'),
                    'purchase_order_id' => $purchaseOrder->id,
                    'item_id' => $request->input('purchase_items.' . $index . '.item_id'),
                ]);

                array_push($purchaseItems, $purchaseItem->id);
            }
        }

        // Remove one by one to make sure observer is called
        $tempPurchaseItems = $purchaseOrder->purchaseItems()->whereNotIn('id', $purchaseItems)->get();
        foreach ($tempPurchaseItems as $tempPurchaseItem) $tempPurchaseItem->delete();

        $purchaseFulfillments = [];
        if ($request->purchase_fulfillments) {
            for ($index = 0; $index < count($request->purchase_fulfillments); $index++) {
                $purchaseFulfillment = PurchaseFulfillment::updateOrCreate([
                    'id' => $request->input('purchase_fulfillments.' . $index . '.id'),
                ], [
                    'description' => $request->input('purchase_fulfillments.' . $index . '.description'),
                    'qty' => $request->input('purchase_fulfillments.' . $index . '.qty'),
                    'fulfilled_date' => $request->input('purchase_fulfillments.' . $index . '.fulfilled_date'),
                    'location_id' => $request->input('purchase_fulfillments.' . $index . '.location_id'),
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_item_id' => $request->input('purchase_fulfillments.' . $index . '.purchase_item_id'),
                ]);

                array_push($purchaseFulfillments, $purchaseFulfillment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $temppurchaseFulfillments = $purchaseOrder->purchaseFulfillments()->whereNotIn('id', $purchaseFulfillments)->get();
        foreach ($temppurchaseFulfillments as $temppurchaseFulfillment) $temppurchaseFulfillment->delete();

        $purchasePayments = [];
        if ($request->purchase_payments) {
            for ($index = 0; $index < count($request->purchase_payments); $index++) {
                $purchasePayment = PurchasePayment::updateOrCreate([
                    'id' => $request->input('purchase_payments.' . $index . '.id'),
                ], [
                    'payment_method' => $request->input('purchase_payments.' . $index . '.payment_method'),
                    'description' => $request->input('purchase_payments.' . $index . '.description'),
                    'amount' => $request->input('purchase_payments.' . $index . '.amount'),
                    'payment_ref_no' => $request->input('purchase_payments.' . $index . '.payment_ref_no'),
                    'payment_date' => $request->input('purchase_payments.' . $index . '.payment_date'),
                    'purchase_order_id' => $purchaseOrder->id,
                ]);

                array_push($purchasePayments, $purchasePayment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempPurchasePayments = $purchaseOrder->purchasePayments()->whereNotIn('id', $purchasePayments)->get();
        foreach ($tempPurchasePayments as $tempPurchasePayment) $tempPurchasePayment->delete();

        // update status
        $this->refreshFulfillmentStatus($purchaseOrder);
        $this->refreshPaymentStatus($purchaseOrder);
        $purchaseOrder->save();

        return new PurchaseOrderResource($purchaseOrder->load(['location', 'supplier', 'purchaseFulfillments', 'purchaseItems', 'supplierAddress', 'purchaseItems.item', 'purchaseFulfillments.location', 'purchasePayments','purchaseFulfillments.purchaseItem.item']));
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(PurchaseOrder $purchaseOrder)
    {
        foreach($purchaseOrder->purchaseFulfillments as $purchaseFulfillment) {
            $purchaseFulfillment->delete();
        }

        foreach($purchaseOrder->purchaseItems as $purchaseItem) {
            $purchaseItem->delete();
        }

        foreach($purchaseOrder->purchasePayments as $purchasePayment) {
            $purchasePayment->delete();
        }

        $purchaseOrder->delete();

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
        $purchaseOrder = PurchaseOrder::withTrashed()->findOrFail($id);
        if ($purchaseOrder->trashed()) $purchaseOrder->restore();

        return new PurchaseOrderResource($purchaseOrder);
    }

    public function fulfill(Request $request, $purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);

        $purchaseFulfillments = [];
        if ($request->purchase_fulfillments) {
            for ($index = 0; $index < count($request->purchase_fulfillments); $index++) {
                $purchaseFulfillment = PurchaseFulfillment::updateOrCreate([
                    'id' => $request->input('purchase_fulfillments.' . $index . '.id'),
                ], [
                    'description' => $request->input('purchase_fulfillments.' . $index . '.description'),
                    'qty' => $request->input('purchase_fulfillments.' . $index . '.qty'),
                    'fulfilled_date' => $request->input('purchase_fulfillments.' . $index . '.fulfilled_date'),
                    'location_id' => $request->input('purchase_fulfillments.' . $index . '.location_id'),
                    'purchase_order_id' => $purchaseOrder->id,
                    'purchase_item_id' => $request->input('purchase_fulfillments.' . $index . '.purchase_item_id'),
                ]);

                array_push($purchaseFulfillments, $purchaseFulfillment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempPurchaseFulfillments = $purchaseOrder->purchaseFulfillments()->whereNotIn('id', $purchaseFulfillments)->get();
        foreach ($tempPurchaseFulfillments as $tempPurchaseFulfillment) $tempPurchaseFulfillment->delete();

        // update status
        $this->refreshFulfillmentStatus($purchaseOrder);
        $purchaseOrder->save();

        return new PurchaseOrderResource($purchaseOrder->load(['location', 'supplier', 'purchaseFulfillments', 'purchaseItems', 'supplierAddress', 'purchaseItems.item', 'purchaseFulfillments.location', 'purchasePayments','purchaseFulfillments.purchaseItem.item']));
    }

    public function pay(Request $request, $purchaseOrderId)
    {
        $purchaseOrder = PurchaseOrder::findOrFail($purchaseOrderId);

        $purchasePayments = [];
        if ($request->purchase_payments) {
            for ($index = 0; $index < count($request->purchase_payments); $index++) {
                $purchasePayment = PurchasePayment::updateOrCreate([
                    'id' => $request->input('purchase_payments.' . $index . '.id'),
                ], [
                    'description' => $request->input('purchase_payments.' . $index . '.description'),
                    'payment_date' => $request->input('purchase_payments.' . $index . '.payment_date'),
                    'amount' => $request->input('purchase_payments.' . $index . '.amount'),
                    'payment_method' => $request->input('purchase_payments.' . $index . '.payment_method'),
                    'purchase_order_id' => $purchaseOrder->id,
                ]);

                array_push($purchasePayments, $purchasePayment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempPrchasePayments = $purchaseOrder->purchasePayments()->whereNotIn('id', $purchasePayments)->get();
        foreach ($tempPrchasePayments as $tempPurchasePayment) $tempPurchasePayment->delete();

        // update status
        $this->refreshPaymentStatus($purchaseOrder);
        $purchaseOrder->save();

        return new PurchaseOrderResource($purchaseOrder->load(['location', 'supplier', 'purchaseFulfillments', 'purchaseItems', 'supplierAddress', 'purchaseItems.item', 'purchaseFulfillments.location', 'purchasePayments','purchaseFulfillments.purchaseItem.item']));
    }

    /**
     * Progress:
     * 
     *     0%        n%       100%     >100%
     *  Un-paid    On-going   Paid   Over-paid       
     */
    private function refreshPaymentStatus(PurchaseOrder $purchaseOrder)
    {
        $totalPrice = 0;
        foreach ($purchaseOrder->purchaseItems as $purchaseItem) {
            $totalPrice += $purchaseItem->getSubTotal();
        }

        $totalPayment = 0;
        foreach ($purchaseOrder->purchasePayments as $purchasePayment) {
            $totalPayment += $purchasePayment->amount;
        }

        if ($totalPayment > $totalPrice) {
            $purchaseOrder->payment_status = "Overpaid";
        } else if ($totalPayment == $totalPrice) {
            $purchaseOrder->payment_status = "Paid";
        } else if ($totalPayment < $totalPrice && $totalPayment > 0) {
            $purchaseOrder->payment_status = "On-going payment";
        } else {
            $purchaseOrder->payment_status = "Unpaid";
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
    private function refreshFulfillmentStatus(PurchaseOrder $purchaseOrder)
    {
        $purchaseItemQty = 0;
        foreach ($purchaseOrder->purchaseItems as $purchaseItem) {
            $purchaseItemQty += $purchaseItem->qty;
        }

        $purchaseFulfillmentQty = 0;
        foreach ($purchaseOrder->purchaseFulfillments as $purchaseFulfillment) {
            $purchaseFulfillmentQty += $purchaseFulfillment->qty;
        }

        if ($purchaseItemQty == $purchaseFulfillmentQty) {
            $purchaseOrder->fulfillment_status = "Fulfilled";
        } else if ($purchaseItemQty < $purchaseFulfillmentQty) {
            $purchaseOrder->fulfillment_status = "Overfulfilled";
        } else if (($purchaseFulfillmentQty > 0) && ($purchaseFulfillmentQty < $purchaseItemQty)) {
            $purchaseOrder->fulfillment_status = "On-going fulfilment";
        } else {
            $purchaseOrder->fulfillment_status = "Unfulfilled";
        }
    }
}

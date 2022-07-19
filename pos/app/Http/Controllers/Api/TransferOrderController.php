<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersDateRangeOrderedAt;
use App\Http\Common\Filter\FiltersLimit;
use App\Http\Common\Filter\FiltersSoftDelete;
use App\Http\Common\Filter\FiltersStatus;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\TransferOrder;
use App\Http\Requests\TransferOrderStoreRequest as StoreRequest;
use App\Http\Requests\TransferOrderUpdateRequest as UpdateRequest;
use App\Http\Resources\TransferOrderResource;
use App\Models\Location;
use App\Models\TransferItem;
use Carbon\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class TransferOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $transferOrders = QueryBuilder::for(TransferOrder::class)
            ->allowedFilters([
                AllowedFilter::custom('start-between', new FiltersDateRangeOrderedAt),
                AllowedFilter::custom('limit', new FiltersLimit),
                AllowedFilter::custom('status', new FiltersStatus)
            ])
            ->allowedIncludes(['transfer_items','transfer_items.item', 'transfer_items.current_location', 'transfer_items.destination_location'])
            ->get();
        return TransferOrderResource::collection($transferOrders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * Create new transfer order with the transfer items 
     * 
     * bodyParam:
     * {
     *   ordered_at: "value",
     *   remark: "value",
     *   status: "value",
     *   transfer_items: [
     *     {
     *       description: "value",
     *       current_location: "value",
     *       destination_location: "value",
     *       qty: "value",
     *       item_id: "value"
     *     }
     *   ]
     * } 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        //first status are Open
        $transferOrder = TransferOrder::create([
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'status' => 'Open',
            'sent_at' => $request->input('sent_at'),
            'received_at' => $request->input('received_at'),
            'cancelled_at' => $request->input('cancelled_at'),
        ]);

        if ($request->transfer_items) {
            for ($index = 0; $index < count($request->transfer_items); $index++) {
                TransferItem::create([
                    'transfer_order_id' => $transferOrder->id,
                    'item_id' => $request->input('transfer_items.' . $index . '.item_id'),
                    'current_location_id' => $request->input('transfer_items.' . $index . '.current_location_id'),
                    'destination_location_id' => $request->input('transfer_items.' . $index . '.destination_location_id'),
                    'description' => $request->input('transfer_items.' . $index . '.description'),
                    'qty' => $request->input('transfer_items.' . $index . '.qty'),
                    'status' => $transferOrder->status
                ]);
            }
        }

        return new TransferOrderResource($transferOrder->load(['transferItems','transferItems.currentLocation','transferItems.destinationLocation','transferItems.item']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = TransferOrder::where('id', $id);
        $transferOrder = QueryBuilder::for($query)
            ->allowedIncludes(['transfer_items','transfer_items.item', 'transfer_items.current_location', 'transfer_items.destination_location'])
            ->firstOrFail();
        return new TransferOrderResource($transferOrder);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, TransferOrder $transferOrder)
    {
        $transferOrder->update([
            'transfer_ref_no' => $request->input('transfer_ref_no'),
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'status' => $request->input('status'),
            'sent_at' => $request->input('sent_at'),
            'received_at' => $request->input('received_at'),
            'cancelled_at' => $request->input('cancelled_at'),
        ]);

        $transferItems = [];
        if ($request->transfer_items) {
            for ($index = 0; $index < count($request->transfer_items); $index++) {
                $transferItem = TransferItem::updateOrCreate([
                    'id' => $request->input('transfer_items.' . $index . '.id'),
                ], [
                    'transfer_order_id' => $transferOrder->id,
                    'item_id' => $request->input('transfer_items.' . $index . '.item_id'),
                    'current_location_id' => $request->input('transfer_items.' . $index . '.current_location_id'),
                    'destination_location_id' => $request->input('transfer_items.' . $index . '.destination_location_id'),
                    'description' => $request->input('transfer_items.' . $index . '.description'),
                    'qty' => $request->input('transfer_items.' . $index . '.qty'),
                    'status' => $transferOrder->status
                ]);

                array_push($transferItems, $transferItem->id);
            }
        }
        // Remove one by one to make sure observer is called
        $temptransferItems = $transferOrder->transferItems()->whereNotIn('id', $transferItems)->get();
        foreach ($temptransferItems as $temptransferItem) $temptransferItem->delete();
        
        return new TransferOrderResource($transferOrder->load(['transferItems','transferItems.currentLocation','transferItems.destinationLocation','transferItems.item']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(TransferOrder $transferOrder)
    {
        $transferOrder->delete();

        return response()->json(null, 204);
    }

    public function cancelTransferOrder(UpdateRequest $request, $transferOrderId)
    {
        $transferOrder = TransferOrder::where('id','=',$transferOrderId)->first();
        $transferOrder->update([
            'transfer_ref_no' => $request->input('transfer_ref_no'),
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'status' => $request->input('status'),
            'cancelled_at' => Carbon::now()
        ]);

        $transferItems = [];
        if ($request->transfer_items) {
            for ($index = 0; $index < count($request->transfer_items); $index++) {
                $transferItem = TransferItem::updateOrCreate([
                    'id' => $request->input('transfer_items.' . $index . '.id'),
                ], [
                    'transfer_order_id' => $transferOrderId,
                    'item_id' => $request->input('transfer_items.' . $index . '.item_id'),
                    'current_location_id' => $request->input('transfer_items.' . $index . '.current_location_id'),
                    'destination_location_id' => $request->input('transfer_items.' . $index . '.destination_location_id'),
                    'description' => $request->input('transfer_items.' . $index . '.description'),
                    'qty' => $request->input('transfer_items.' . $index . '.qty'),
                    'status' => $request->input('transfer_items.' . $index . '.status'),
                ]);

                array_push($transferItems, $transferItem->id);
            }
        }
        // Remove one by one to make sure observer is called
        $temptransferItems = $transferOrder->transferItems()->whereNotIn('id', $transferItems)->get();
        foreach ($temptransferItems as $temptransferItem) $temptransferItem->delete();

        return new TransferOrderResource($transferOrder->load(['transferItems','transferItems.currentLocation','transferItems.destinationLocation','transferItems.item']));
    }
}

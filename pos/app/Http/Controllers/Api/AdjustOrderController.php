<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersDateRangeOrderedAt;
use App\Http\Common\Filter\FiltersLimit;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\AdjustOrder;
use App\Http\Requests\AdjustOrderStoreRequest as StoreRequest;
use App\Http\Requests\AdjustOrderUpdateRequest as UpdateRequest;
use App\Http\Resources\AdjustOrderResource;
use App\Models\AdjustItem;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AdjustOrderController extends Controller
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
        $adjustOrders = QueryBuilder::for(AdjustOrder::class)
            ->allowedFilters([
                AllowedFilter::custom('start-between', new FiltersDateRangeOrderedAt),
                AllowedFilter::custom('limit', new FiltersLimit),
                AllowedFilter::exact('status')
            ])
            ->allowedIncludes(['adjust_items','adjust_items.item','adjust_items.location'])
            ->get();
        return AdjustOrderResource::collection($adjustOrders);
    }

    /**
     * Show the form for creating a new resource.
     *
     * Create new adjust order with the adjust items 
     * 
     * bodyParam:
     * {
     *   ordered_at: "value",
     *   remark: "value",
     *   adjust_items: [
     *     {
     *       description: "value",
     *       qty: "value",
     *       item_id: "value",
     *       location_id: "value"
     *     }
     *   ]
     * } 
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        //first status are Completed
        $adjustOrder = AdjustOrder::create([
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'status' => 'Completed',
        ]);

        if ($request->adjust_items) {
            for ($index = 0; $index < count($request->adjust_items); $index++) {
                AdjustItem::create([
                    'description' => $request->input('adjust_items.' . $index . '.description'),
                    'old_qty' => $request->input('adjust_items.' . $index . '.old_qty'),
                    'location_id' => $request->input('adjust_items.' . $index . '.location_id'),
                    'adjust_order_id' => $adjustOrder->id,
                    'item_id' => $request->input('adjust_items.' . $index . '.item_id'),
                    'difference' => $request->input('adjust_items.' . $index . '.difference'),
                    'status' => $adjustOrder->status
                ]);
            }
        }

        return new AdjustOrderResource($adjustOrder->load(['adjustItems','adjustItems.item','adjustItems.location']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = AdjustOrder::where('id', $id);
        $adjustOrder = QueryBuilder::for($query)
            ->allowedIncludes(['adjust_items','adjust_items.item','adjust_items.location'])
            ->firstOrFail();
        return new AdjustOrderResource($adjustOrder);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, AdjustOrder $adjustOrder)
    {
        $adjustOrder->update([
            'adjust_ref_no' => $request->input('adjust_ref_no'),
            'ordered_at' => $request->input('ordered_at'),
            'remark' => $request->input('remark'),
            'status' => $request->input('status'),
        ]);
        
        $adjustItems = [];
        if ($request->adjust_items) {
            for ($index = 0; $index < count($request->adjust_items); $index++) {
                $adjustItem = AdjustItem::updateOrCreate([
                    'id' => $request->input('adjust_items.' . $index . '.id'),
                ], [
                    'description' => $request->input('adjust_items.' . $index . '.description'),
                    'old_qty' => $request->input('adjust_items.' . $index . '.old_qty'),
                    'adjust_order_id' => $adjustOrder->id,
                    'location_id' => $request->input('adjust_items.' . $index . '.location_id'),
                    'item_id' => $request->input('adjust_items.' . $index . '.item_id'),
                    'difference' => $request->input('adjust_items.' . $index . '.difference'),
                    'status' => $adjustOrder->status
                ]);

                array_push($adjustItems, $adjustItem->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempAdjustItems = $adjustOrder->adjustItems()->whereNotIn('id', $adjustItems)->get();
        foreach ($tempAdjustItems as $tempAdjustItem) $tempAdjustItem->delete();

        return new AdjustOrderResource($adjustOrder->load(['adjustItems','adjustItems.item','adjustItems.location']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdjustOrder $adjustOrder)
    {
        $adjustOrder->delete();

        return response()->json(null, 204);
    }
}

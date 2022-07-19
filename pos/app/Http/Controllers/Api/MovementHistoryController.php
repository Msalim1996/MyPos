<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersDateRange;
use App\Http\Common\Filter\FiltersLimit;
use App\Http\Common\Filter\FiltersMoveableType;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\MovementHistory;
use App\Http\Resources\MovementHistoryResource;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MovementHistoryController extends Controller
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
        $movementHistory = QueryBuilder::for(MovementHistory::class)
            ->allowedFilters([
                AllowedFilter::custom('start-between', new FiltersDateRange),
                AllowedFilter::custom('limit', new FiltersLimit),
                AllowedFilter::exact('moveable_type'),
                AllowedFilter::exact('item_id')
            ])
            // TODO: to be continued
            //->allowedIncludes()
            ->get();
        return MovementHistoryResource::collection($movementHistory->load(['item']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = MovementHistory::where('id', $id);
        $movementHistory = QueryBuilder::for($query)
            ->allowedIncludes(['item','transfer_stock','adjust_order','sales_fulfillment','sales_restock','sales_return'])
            ->firstOrFail();
        return new MovementHistoryResource($movementHistory);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}

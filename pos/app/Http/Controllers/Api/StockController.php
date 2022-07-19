<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StockRequest as StoreRequest;
use App\Http\Requests\StockRequest as UpdateRequest;
use App\Http\Resources\StockResource;
use App\Models\Stock;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Stock CRUD
 */
class StockController extends Controller
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
        $stocks = QueryBuilder::for(Stock::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
                'item_id'
            ])
            ->allowedIncludes(['location', 'item'])
            ->get();
        return StockResource::collection($stocks);
    }

    /**
     * POST
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $stock = Stock::create([
            'qty' => $request->qty,
            'item_id' => $request->item_id,
            'location_id' => $request->location_id,
        ]);

        return new StockResource($stock);
    }

    /**
     * GET
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = Stock::withTrashed()->where('id', $id);
        $stock = QueryBuilder::for($query)
            ->allowedIncludes(['location', 'item'])
            ->firstOrFail();
        return new StockResource($stock);
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
    public function update(UpdateRequest $request, Stock $stock)
    {
        $stock->update($request->only(['qty', 'item_id', 'location_id']));

        return new StockResource($stock);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

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
        $stock = Stock::withTrashed()->findOrFail($id);
        if ($stock->trashed()) $stock->restore();

        return new StockResource($stock);
    }
}

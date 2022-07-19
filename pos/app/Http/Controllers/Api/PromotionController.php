<?php

namespace App\Http\Controllers\api;

use App\Enums\DiscountType;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PromotionStoreRequest as StoreRequest;
use App\Http\Requests\PromotionUpdateRequest as UpdateRequest;
use App\Http\Resources\PromotionResource;
use App\Models\Promotion;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PromotionController extends Controller
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
        $promotion = QueryBuilder::for(Promotion::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['pre_item', 'benefit_item'])
            ->get();
        return PromotionResource::collection($promotion);
    }

    /**
     * POST
     * 
     * Create new promotion
     * 
     * bodyParam:
     * {
     *      pre_qty                 : int,
     *      pre_item_id             : int,
     *      pre_type                : string,
     *      benefit_qty             : int,
     *      benefit_item_id         : int,
     *      benefit_discount_amount : decimal,
     *      benefit_discount_type   : string,
     *      apply_multiply          : boolean
     * }
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $promotion = Promotion::create([
            'name' => $request->input('name'),
            'pre_qty' => $request->input('pre_qty'),
            'pre_item_id' => $request->input('pre_item_id'),
            'pre_type' => $request->input('pre_type'),
            'benefit_qty' => $request->input('benefit_qty'),
            'benefit_item_id' => $request->input('benefit_item_id'),
            'benefit_discount_amount' => $request->input('benefit_discount_amount'),
            'benefit_discount_type' => $request->input('benefit_discount_type'),
            'benefit_type' => $request->input('benefit_type'),
            'apply_multiply' => $request->input('apply_multiply')
        ]);

        return new PromotionResource($promotion);
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
        $query = Promotion::withTrashed()->where('id', $id);
        $promotion = QueryBuilder::for($query)
            ->allowedIncludes(['pre_item', 'benefit_item'])
            ->firstOrFail();
        return new PromotionResource($promotion);
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
    public function update(UpdateRequest $request, Promotion $promotion)
    {
        $promotion->update([
            'name' => $request->input('name'),
            'pre_qty' => $request->input('pre_qty'),
            'pre_item_id' => $request->input('pre_item_id'),
            'pre_type' => $request->input('pre_type'),
            'benefit_qty' => $request->input('benefit_qty'),
            'benefit_item_id' => $request->input('benefit_item_id'),
            'benefit_discount_amount' => $request->input('benefit_discount_amount'),
            'benefit_discount_type' => $request->input('benefit_discount_type'),
            'benefit_type' => $request->input('benefit_type'),
            'apply_multiply' => $request->input('apply_multiply')
        ]);

        return new PromotionResource($promotion->load(['preItem', 'benefitItem']));
    }

    /**
     * DELETE
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Promotion $promotion)
    {
        $promotion->delete();

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
        $promotion = Promotion::withTrashed()->findOrFail($id);
        if ($promotion->trashed()) $promotion->restore();

        return new PromotionResource($promotion->load(['preItem', 'benefitItem']));
    }

    /**
     * GET item types
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
}

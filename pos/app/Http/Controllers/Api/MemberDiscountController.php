<?php

namespace App\Http\Controllers\api;

use App\Enums\DiscountType;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\MemberDiscountStoreRequest as StoreRequest;
use App\Http\Requests\MemberDiscountUpdateRequest as UpdateRequest;
use App\Http\Resources\MemberDiscountResource;
use App\Models\MemberDiscount;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MemberDiscountController extends Controller
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
        $memberDiscount = QueryBuilder::for(MemberDiscount::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['item'])
            ->get();
        return MemberDiscountResource::collection($memberDiscount);
    }

    /**
     * POST
     * 
     * Create new member discount
     * 
     * bodyParam:
     * {
     *      item_id                 : int,
     *      discount_amount         : decimal,
     *      discount_type           : string
     * }
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $memberDiscount = MemberDiscount::create([
            'item_id' => $request->input('item_id'),
            'discount_amount' => $request->input('discount_amount'),
            'discount_type' => $request->input('discount_type')
        ]);
        return new MemberDiscountResource($memberDiscount->load(['item']));
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
        $query = MemberDiscount::withTrashed()->where('id', $id);
        $memberDiscount = QueryBuilder::for($query)
            ->allowedIncludes(['item'])
            ->firstOrFail();
        return new MemberDiscountResource($memberDiscount);
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
    public function update(UpdateRequest $request, MemberDiscount $memberDiscount)
    {
        $memberDiscount->update([
            'item_id' => $request->input('item_id'),
            'discount_amount' => $request->input('discount_amount'),
            'discount_type' => $request->input('discount_type')
        ]);
        return new MemberDiscountResource($memberDiscount->load(['item']));
    }

    /**
     * DELETE
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(MemberDiscount $memberDiscount)
    {
        $memberDiscount->delete();

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
        $memberDiscount = MemberDiscount::withTrashed()->findOrFail($id);
        if ($memberDiscount->trashed()) $memberDiscount->restore();

        return new MemberDiscountResource($memberDiscount->load(['item']));
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

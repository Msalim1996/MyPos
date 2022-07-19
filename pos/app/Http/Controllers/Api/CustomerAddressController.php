<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerAddressStoreRequest as StoreRequest;
use App\Http\Requests\CustomerAddressUpdateRequest as UpdateRequest;
use App\Http\Resources\CustomerAddressResource;
use App\Models\CustomerAddress;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group CustomerAddress CRUD
 */
class CustomerAddressController extends Controller
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
        $customerAddresses = QueryBuilder::for(CustomerAddress::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
                'customer_id'
            ])
            ->get();
        return CustomerAddressResource::collection($customerAddresses);
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
        $customerAddress = CustomerAddress::create([
            'name' => $request->name,
            'street' => $request->street,
            'city' => $request->city,
            'state' => $request->state,
            'zip' => $request->zip,
            'country' => $request->country,
            'remark' => $request->remark,
            'type' => $request->type,
            'default_billing_address' => $request->default_billing_address,
            'default_shipping_address' => $request->default_shipping_address,
            'customer_id' => $request->customer_id,
        ]);

        return new CustomerAddressResource($customerAddress);
    }

    /**
     * GET
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(CustomerAddress $customerAddress)
    {
        return new CustomerAddressResource($customerAddress);
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
    public function update(UpdateRequest $request, CustomerAddress $customerAddress)
    {
        $customerAddress->update($request->only(['name', 'street', 'city', 'state', 'zip', 'country', 'remark', 'type', 'default_billing_address', 'default_shipping_address', 'customer_id']));

        return new CustomerAddressResource($customerAddress);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CustomerAddress $customerAddress)
    {
        $customerAddress->delete();

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
        $customerAddress = CustomerAddress::withTrashed()->findOrFail($id);
        if ($customerAddress->trashed()) $customerAddress->restore();

        return new CustomerAddressResource($customerAddress);
    }
}

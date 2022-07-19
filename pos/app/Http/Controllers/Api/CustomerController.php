<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerStoreRequest as StoreRequest;
use App\Http\Requests\CustomerUpdateRequest as UpdateRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use App\Models\CustomerAddress;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group Customer CRUD
 */
class CustomerController extends Controller
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
        $customers = QueryBuilder::for(Customer::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['customer_addresses'])
            ->get();
        return CustomerResource::collection($customers);
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
        $customer = Customer::create([
            'name' => $request->name,
            'fax' => $request->fax,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'remark' => $request->remark,
        ]);

        if ($request->customer_addresses) {
            for ($index = 0; $index < count($request->customer_addresses); $index++) {
                CustomerAddress::create([
                    'name' => $request->input('customer_addresses.' . $index . '.name'),
                    'street' => $request->input('customer_addresses.' . $index . '.street'),
                    'city' => $request->input('customer_addresses.' . $index . '.city'),
                    'state' => $request->input('customer_addresses.' . $index . '.state'),
                    'zip' => $request->input('customer_addresses.' . $index . '.zip'),
                    'country' => $request->input('customer_addresses.' . $index . '.country'),
                    'remark' => $request->input('customer_addresses.' . $index . '.remark'),
                    'type' => $request->input('customer_addresses.' . $index . '.type'),
                    'default_billing_address' => $request->input('customer_addresses.' . $index . '.default_billing_address'),
                    'default_shipping_address' => $request->input('customer_addresses.' . $index . '.default_shipping_address'),
                    'customer_id' => $customer->id,
                ]);
            }
        }

        return new CustomerResource($customer);
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
        $query = Customer::withTrashed()->where('id', $id);
        $customer = QueryBuilder::for($query)
            ->allowedIncludes(['customer_addresses'])
            ->firstOrFail();
        return new CustomerResource($customer);
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
    public function update(UpdateRequest $request, Customer $customer)
    {
        $customer->update([
            'name' => $request->input('name'),
            'fax' => $request->input('fax'),
            'phone' => $request->input('phone'),
            'email' => $request->input('email'),
            'website' => $request->input('website'),
            'remark' => $request->input('remark'),
        ]);

        $customerAddresses = [];
        if ($request->customer_addresses) {
            for ($index = 0; $index < count($request->customer_addresses); $index++) {
                $customerAddress = CustomerAddress::updateOrCreate([
                    'id' => $request->input('customer_addresses.' . $index . '.id'),
                ], [
                    'name' => $request->input('customer_addresses.' . $index . '.name'),
                    'street' => $request->input('customer_addresses.' . $index . '.street'),
                    'city' => $request->input('customer_addresses.' . $index . '.city'),
                    'state' => $request->input('customer_addresses.' . $index . '.state'),
                    'zip' => $request->input('customer_addresses.' . $index . '.zip'),
                    'country' => $request->input('customer_addresses.' . $index . '.country'),
                    'remark' => $request->input('customer_addresses.' . $index . '.remark'),
                    'type' => $request->input('customer_addresses.' . $index . '.type'),
                    'default_billing_address' => $request->input('customer_addresses.' . $index . '.default_billing_address'),
                    'default_shipping_address' => $request->input('customer_addresses.' . $index . '.default_shipping_address'),
                    'customer_id' => $customer->id,
                ]);

                array_push($customerAddresses, $customerAddress->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempCustomerAddresses = $customer->customerAddresses()->whereNotIn('id', $customerAddresses)->get();
        foreach ($tempCustomerAddresses as $tempCustomerAddress) $tempCustomerAddress->delete();

        $customer->save();

        return new CustomerResource($customer);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        $customer->delete();

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
        $customer = Customer::withTrashed()->findOrFail($id);
        if ($customer->trashed()) $customer->restore();

        return new CustomerResource($customer);
    }
}

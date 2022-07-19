<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use App\Http\Resources\SupplierAddressResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierAddressStoreRequest as StoreRequest;
use App\Http\Requests\SupplierAddressUpdateRequest as UpdateRequest;
use App\Models\SupplierAddress;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SupplierAddressController extends Controller
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
        $supplierAddress = QueryBuilder::for(SupplierAddress::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
                'supplier_id'
            ])
            ->get();
        return SupplierAddressResource::collection($supplierAddress);
    }

    /**
     * POST
     * 
     * Create new supplier address
     * 
     * bodyParam:
     * {
     *      name                    : string,
     *      street                  : string,
     *      city                    : string,
     *      zip                     : string,
     *      country                 : string,
     *      remark                  : string,
     *      type                    : string,
     *      default_billing_address : boolean,
     *      default_shipping_address: boolean,
     *      supplier_id             : int
     * }
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $supplierAddress = SupplierAddress::create([
            'name' => $request->input('name'),
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'remark' => $request->input('remark'),
            'type' => $request->input('type'),
            'default_billing_address' => $request->input('default_billing_address'),
            'default_shipping_address' => $request->input('default_shipping_address'),
            'supplier_id' => $request->input('supplier_id')
        ]);
        
        return new SupplierAddressResource($supplierAddress);
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
        $query = SupplierAddress::withTrashed()->where('id', $id);
        $supplierAddress = QueryBuilder::for($query)
            ->allowedIncludes(['supplier'])
            ->firstOrFail();
        return new SupplierAddressResource($supplierAddress);
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
    public function update(Request $request, SupplierAddress $supplierAddress)
    {
        $supplierAddress->update([
            'name' => $request->input('name'),
            'street' => $request->input('street'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'zip' => $request->input('zip'),
            'country' => $request->input('country'),
            'remark' => $request->input('remark'),
            'type' => $request->input('type'),
            'default_billing_address' => $request->input('default_billing_address'),
            'default_shipping_address' => $request->input('default_shipping_address'),
            'supplier_id' => $request->input('supplier_id')
        ]);

        return new SupplierAddressResource($supplierAddress);
    }

    /**
     * DELETE
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SupplierAddress $supplierAddress)
    {
        $supplierAddress->delete();

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
        $supplierAddress = SupplierAddress::withTrashed()->findOrFail($id);
        if ($supplierAddress->trashed()) $supplierAddress->restore();

        return new SupplierAddressResource($supplierAddress);
    }
}

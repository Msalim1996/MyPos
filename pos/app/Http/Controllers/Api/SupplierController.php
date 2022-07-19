<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use App\Http\Resources\SupplierResource;
use App\Http\Controllers\Controller;
use App\Http\Requests\SupplierStoreRequest as StoreRequest;
use App\Http\Requests\SupplierUpdateRequest as UpdateRequest;
use App\Models\Supplier;
use App\Models\SupplierAddress;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SupplierController extends Controller
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
        $supplier = QueryBuilder::for(Supplier::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['supplierAddresses'])
            ->get();
        return SupplierResource::collection($supplier);
    }

    /**
     * POST
     * 
     * Create new supplier
     * 
     * bodyParam:
     * {
     *      name                    : string,
     *      phone_number            : string,
     *      description             : string,
     * }
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $supplier = Supplier::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'website' => $request->website,
            'fax' => $request->fax,
            'email' => $request->email,
            'remark' => $request->remark,
        ]);

        if ($request->supplier_addresses) {
            for ($index = 0; $index < count($request->supplier_addresses); $index++) {
                SupplierAddress::create([
                    'name' => $request->input('supplier_addresses.' . $index . '.name'),
                    'street' => $request->input('supplier_addresses.' . $index . '.street'),
                    'city' => $request->input('supplier_addresses.' . $index . '.city'),
                    'state' => $request->input('supplier_addresses.' . $index . '.state'),
                    'zip' => $request->input('supplier_addresses.' . $index . '.zip'),
                    'country' => $request->input('supplier_addresses.' . $index . '.country'),
                    'remark' => $request->input('supplier_addresses.' . $index . '.remark'),
                    'type' => $request->input('supplier_addresses.' . $index . '.type'),
                    'default_billing_address' => $request->input('supplier_addresses.' . $index . '.default_billing_address'),
                    'default_shipping_address' => $request->input('supplier_addresses.' . $index . '.default_shipping_address'),
                    'supplier_id' => $supplier->id,
                ]);
            }
        }

        return new SupplierResource($supplier);
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
        $query = Supplier::withTrashed()->where('id', $id);
        $supplier = QueryBuilder::for($query)
            ->allowedIncludes(['supplierAddresses'])
            ->firstOrFail();
        return new SupplierResource($supplier);
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
    public function update(Request $request, Supplier $supplier)
    {
        $supplier->update([
            'name' => $request->input('name'),
            'phone' => $request->input('phone'),
            'website' => $request->input('website'),
            'fax' => $request->input('fax'),
            'email' => $request->input('email'),
            'remark' => $request->input('remark'),
        ]);

        $supplierAddresses = [];
        if ($request->supplier_addresses) {
            for ($index = 0; $index < count($request->supplier_addresses); $index++) {
                $supplierAddress = SupplierAddress::updateOrCreate([
                    'id' => $request->input('supplier_addresses.' . $index . '.id'),
                ], [
                    'name' => $request->input('supplier_addresses.' . $index . '.name'),
                    'street' => $request->input('supplier_addresses.' . $index . '.street'),
                    'city' => $request->input('supplier_addresses.' . $index . '.city'),
                    'state' => $request->input('supplier_addresses.' . $index . '.state'),
                    'zip' => $request->input('supplier_addresses.' . $index . '.zip'),
                    'country' => $request->input('supplier_addresses.' . $index . '.country'),
                    'remark' => $request->input('supplier_addresses.' . $index . '.remark'),
                    'type' => $request->input('supplier_addresses.' . $index . '.type'),
                    'default_billing_address' => $request->input('supplier_addresses.' . $index . '.default_billing_address'),
                    'default_shipping_address' => $request->input('supplier_addresses.' . $index . '.default_shipping_address'),
                    'supplier_id' => $supplier->id,
                ]);

                array_push($supplierAddresses, $supplierAddress->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempSupplierAddresses = $supplier->supplierAddresses()->whereNotIn('id', $supplierAddresses)->get();
        foreach ($tempSupplierAddresses as $tempSupplierAddress) $tempSupplierAddress->delete();

        return new SupplierResource($supplier);
    }

    /**
     * DELETE
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        
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
        $supplier = Supplier::withTrashed()->findOrFail($id);
        if ($supplier->trashed()) $supplier->restore();

        return new SupplierResource($supplier);
    }
}

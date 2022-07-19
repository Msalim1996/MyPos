<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Resources\BarcodeTypeResource;
use App\Http\Controllers\Controller;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\BarcodeTypeRequest as StoreRequest;
use App\Http\Requests\BarcodeTypeRequest as UpdateRequest;
use App\Models\BarcodeType;

/**
 * @group Barcode Type CRUD
 */
class BarcodeTypeController extends Controller
{
    /**
     * GET all
     * 
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return BarcodeTypeResource::collection(BarcodeType::all());
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
        $barcodeType = BarcodeType::create([
            'prefix' => $request->prefix,
            'type' => $request->type,
            'is_allowed_to_rent_shoe' => $request->is_allowed_to_rent_shoe,
        ]);

        return new BarcodeTypeResource($barcodeType);
    }

    /**
     * GET
     * 
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(BarcodeType $barcodeType)
    {
        return new BarcodeTypeResource($barcodeType);
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
    public function update(UpdateRequest $request, BarcodeType $barcodeType)
    {
        $barcodeType->update($request->only(['prefix', 'type', 'is_allowed_to_rent_shoe']));

        return new BarcodeTypeResource($barcodeType);
    }

    /**
     * DELETE
     * 
     * @authenticated
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(BarcodeType $barcodeType)
    {
        $barcodeType->delete();

        return response()->json(null, 204);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\PaymentMethodRequest as BatchUpdateRequest;
use App\Http\Resources\PaymentMethodResource;
use App\Models\PaymentMethod;

/**
 * @group Location CRUD
 */
class PaymentMethodController extends Controller
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
        return PaymentMethodResource::collection(PaymentMethod::all());
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
    public function batchUpdate(BatchUpdateRequest $request)
    {
        $paymentMethods = [];
        if ($request->data) {
            for ($index = 0; $index < count($request->data); $index++) {
                $paymentMethod = PaymentMethod::updateOrCreate([
                    'id' => $request->input('data.' . $index . '.id'),
                ], [
                    'position_index' => $request->input('data.' . $index . '.position_index'),
                    'name' => $request->input('data.' . $index . '.name'),
                ]);

                array_push($paymentMethods, $paymentMethod->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempPaymentMethods = PaymentMethod::whereNotIn('id', $paymentMethods)->get();
        foreach ($tempPaymentMethods as $tempPaymentMethod) $tempPaymentMethod->delete();

        return PaymentMethodResource::collection(PaymentMethod::all());
    }
}

<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class SalesOrderUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // only allow updates if the user is logged in
        return auth()->check();
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        //
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ordered_at' => 'required|date_format:"Y-m-d H:i:s"',
            'customer_id' => 'required|exists:customers,id',
            'location_id' => 'required|exists:locations,id',
            
            'sales_items.*.qty' => 'required',
            'sales_items.*.unit_price' => 'required',
            'sales_items.*.discount_amount' => 'required',
            'sales_items.*.item_id' => 'required',
            
            'sales_fulfillments.*.qty' => 'required',
            'sales_fulfillments.*.fulfilled_date' => 'required|date_format:"Y-m-d H:i:s"',
            'sales_fulfillments.*.location_id' => 'required',
            'sales_fulfillments.*.sales_item_id' => 'required',
            
            'sales_returns.*.qty' => 'required',
            'sales_returns.*.returned_date' => 'required|date_format:"Y-m-d H:i:s"',
            'sales_returns.*.discard_stock' => 'required',
            'sales_returns.*.sales_item_id' => 'required',
            'sales_returns.*.location_id' => 'required',

            'sales_payments.*.payment_date' => 'required|date_format:"Y-m-d H:i:s"',
            'sales_payments.*.amount' => 'required',
            
            'sales_restocks.*.qty' => 'required',
            'sales_restocks.*.restocked_date' => 'required|date_format:"Y-m-d H:i:s"',
            'sales_restocks.*.sales_item_id' => 'required',
            'sales_restocks.*.location_id' => 'required',
        ];
    }

    /**
     * Get the validation attributes that apply to the request.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            //
        ];
    }

    /**
     * Get the validation messages that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            //
        ];
    }
}

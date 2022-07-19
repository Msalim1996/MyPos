<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class ItemStoreRequest extends FormRequest
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
        $locationIds = [];
        if ($this->stocks) {
            foreach($this->stocks as $stock) {
                array_push($locationIds, $stock['location_id']);
            }

            $locationsCount = array_count_values($locationIds);
            foreach($locationsCount as $key => $value) {
                if ($value > 1) {
                    $validator->errors()->add('message', 'Location may not be duplicated!');
                }
            }
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:items,name',
            'price' => 'required|numeric',
            'purchase_price' => 'required|numeric',
            'image' => 'base64image:500',

            'stocks.*.qty' => 'required',
            'stocks.*.location_id' => 'required|exists:locations,id',
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

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberDiscountUpdateRequest extends FormRequest
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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'item_id' => 'required|exists:items,id',

            'discount_amount' => 'required',
            'discount_type' => 'required'
        ];
    }
}

<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionUpdateRequest extends FormRequest
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
            'name' => 'required',
            'pre_qty' => 'required',
            'pre_type' => 'required',
            'benefit_type' => 'required',
            'pre_item_id' => 'nullable|exists:items,id',
            'benefit_item_id' => 'nullable|exists:items,id',
            'apply_multiply' => 'required|boolean',
        ];
    }
}

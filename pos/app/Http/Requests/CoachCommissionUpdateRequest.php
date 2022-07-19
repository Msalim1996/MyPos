<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CoachCommissionUpdateRequest extends FormRequest
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
            'coach_id' => 'required|exists:coach,id',
            'commission_class' => 'required',
            'commission_percentage' => 'required'
        ];
    }
}

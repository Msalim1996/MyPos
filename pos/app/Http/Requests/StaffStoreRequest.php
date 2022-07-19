<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StaffStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
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
            'enroll_number' => 'required',
            'name' => 'required',
            'position' => 'required',
            'contract_started_on' => 'required|date_format:"Y-m-d H:i:s"',
            'contract_ended_on' => 'required|date_format:"Y-m-d H:i:s"',
            'contract_changed_on' => 'date_format:"Y-m-d H:i:s"',
            'bpjs' => 'required',
            'sp' => 'required'
        ];
    }
}

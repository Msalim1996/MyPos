<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceTransactionUpdateRequest extends FormRequest
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
            'date' => 'required|date_format:"Y-m-d"',
            'staff_id' => 'required|exists:staffs,id',
            'checked_in_on' => 'required|date_format:"H:i"',
            'checked_out_on' => 'required|date_format:"H:i"',
            'work_type' => 'required',
            'absent_type' => 'required'
        ];
    }
}

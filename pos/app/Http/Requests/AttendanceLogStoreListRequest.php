<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AttendanceLogStoreListRequest extends FormRequest
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
            'attendance_logs.*.enroll_number' => 'required',
            'attendance_logs.*.in_out_mode' => 'required',
            'attendance_logs.*.date' => 'required|date_format:"Y-m-d H:i:s"'
        ];
    }
}

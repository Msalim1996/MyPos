<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserRegisterRequest extends FormRequest
{
    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username'    => 'required|unique:'.config('permission.table_names.users', 'users').',username',
            'name'     => 'required',
            'password' => 'required|min:6|confirmed',
            'birthdate' => 'date',
            'date_join' => 'required|date',
            'gender' => 'in:Male,Female',
            'att_id' => 'required',
        ];
    }
}

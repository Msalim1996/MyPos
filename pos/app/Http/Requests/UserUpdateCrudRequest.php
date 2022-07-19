<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserUpdateCrudRequest extends FormRequest
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
    { }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $id = $this->route('user')->id;
        return [
            'username' => 'required|unique:' . config('permission.table_names.users', 'users') . ',username,' . $id . ',id',
            'name' => 'required',
            'password' => 'confirmed',
            'birthdate' => 'date',
            'date_join' => 'required|date',
            'gender' => 'required|in:Male,Female',
        ];
    }
}

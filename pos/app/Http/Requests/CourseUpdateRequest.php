<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;

class CourseUpdateRequest extends FormRequest
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
        $id = $this->route('course')->id;

        return [
            'course_id' => 'required|unique:courses,course_id,' . $id . ',id',
            'name' => 'required',
            'course_type' => 'required',
            'day_type' => 'required',
            'coach_type' => 'required',
            'price' => 'required',
            'number_of_students_from' => 'required',
            'number_of_students_to' => 'required',
            'number_of_lessons' => 'required',
            'level_group_id' => 'required|exists:level_groups,id',
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

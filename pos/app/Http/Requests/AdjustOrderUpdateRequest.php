<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;

class AdjustOrderUpdateRequest extends FormRequest
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
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->adjust_items) {
                //validate location, if it was soft deleted, dont process it
                for ($index = 0; $index < count($this->adjust_items); $index++) {
                    $location = Location::where('id','=', $this->input('adjust_items.' . $index . '.location_id'))->withTrashed()->firstOrFail();
        
                    if ($location->deleted_at != null){
                        $validator->errors()->add('inactive', 'The locations you selected are inactive!');
                    }
                }
            }
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'ordered_at' => 'required|date_format:"Y-m-d H:i:s"',
            
            'adjust_items.*.old_qty' => 'required',
            'adjust_items.*.location_id' => 'required|exists:locations,id',
            'adjust_items.*.item_id' => 'required',
            'adjust_items.*.difference' => 'required|numeric'
        ];
    }
}

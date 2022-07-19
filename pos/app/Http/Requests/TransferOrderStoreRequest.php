<?php

namespace App\Http\Requests;

use App\Models\Location;
use Illuminate\Foundation\Http\FormRequest;

class TransferOrderStoreRequest extends FormRequest
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
            if ($this->transfer_items) {
                //validate current and destination location, if it was soft deleted, dont process it
                for ($index = 0; $index < count($this->transfer_items); $index++) {
                    $currentLocation = Location::where('id','=',$this->input('transfer_items.' . $index . '.current_location_id'))->withTrashed()->firstOrFail();
                    $destinationLocation = Location::where('id','=',$this->input('transfer_items.' . $index . '.destination_location_id'))->withTrashed()->firstOrFail();
        
                    if ($currentLocation->deleted_at != null || $destinationLocation->deleted_at != null){
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

            'transfer_items.*.item_id' => 'required|exists:items,id',
            'transfer_items.*.current_location_id' => 'required|exists:locations,id',
            'transfer_items.*.destination_location_id' => 'required|exists:locations,id',
            'transfer_items.*.qty' => 'required|numeric'
        ];
    }
}

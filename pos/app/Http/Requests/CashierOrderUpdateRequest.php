<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\BarcodeType;
use Illuminate\Foundation\Http\FormRequest;

class CashierOrderUpdateRequest extends FormRequest
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
    {
        $validator->after(function ($validator) {
            if ($this->tickets) {
                for ($index = 0; $index < count($this->tickets); $index++) {
                    $barcodeId = $this->input('tickets.' . $index . '.barcode_id');
                    $barcodeSplitArr = preg_split('/(?<=[\sa-zA-Z])(?=[0-9])/', $barcodeId);
                    if (count($barcodeSplitArr) != 2 && preg_match('/[^A-Za-z]/', $barcodeSplitArr[0])) {
                        return response()->json(['message' => 'Format barcode salah'], 404);
                    }
                    $prefix = $barcodeSplitArr[0];
                    $barcodeType = BarcodeType::find($prefix);

                    if ($barcodeType == null) {
                        $validator->errors()->add('tickets.' . $index . '.barcode_id', 'Barcode type ' . $prefix . ' is not valid.');
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
            'sales_items.*.qty' => 'required',
            'sales_items.*.unit_price' => 'required',
            'sales_items.*.item_id' => 'required',

            // checking if the barcode id is duplicated is not successful. For now commented it out first
            // 'tickets.*.barcode_id' => 'required|unique:barcodes,barcode_id,' . 'tickets.*.id' . ',id',
            'tickets.*.barcode_id' => 'required',
            'tickets.*.active_on' => 'required|date_format:"Y-m-d H:i:s"',
            'tickets.*.session_name' => 'required',
            'tickets.*.session_day' => 'required',
            'tickets.*.session_start_time' => 'required|date_format:"H:i:s"',
            'tickets.*.session_end_time' => 'required|date_format:"H:i:s"',
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

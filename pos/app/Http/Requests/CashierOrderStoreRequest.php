<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;
use App\Models\BarcodeType;
use App\Models\Item;
use App\Models\Promotion;
use Illuminate\Foundation\Http\FormRequest;

class CashierOrderStoreRequest extends FormRequest
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
                        $validator->errors()->add('message', 'Format barcode salah!');
                    }
                    $prefix = $barcodeSplitArr[0];
                    $barcodeType = BarcodeType::find($prefix);

                    if ($barcodeType == null) {
                        $validator->errors()->add('tickets.' . $index . '.barcode_id', 'Barcode type ' . $prefix . ' is not valid.');
                    }
                }
            }

            if ($this->salesItems) {
                for ($index = 0; $index < count($this->salesItems); $index++) {
                    switch (strtolower($this->input('sales_items.' . $index . '.item_type'))) {
                        case 'item':
                            if (!(Item::where('id', '=', $this->input('sales_items.' . $index . '.item_id')->exists()))) {
                                $validator->errors()->add('message', 'Item ID tidak ada!');
                            }
                            break;

                        case 'promotion':
                            if (!(Promotion::where('id', '=', $this->input('sales_items.' . $index . '.item_id')->exists()))) {
                                $validator->errors()->add('message', 'Promotion ID tidak ada!');
                            }
                            break;

                        case 'student enrollment':
                            if (!(Promotion::where('id', '=', $this->input('sales_items.' . $index . '.item_id')->exists()))) {
                                $validator->errors()->add('message', 'Student enrollment ID tidak ada!');
                            }
                            break;

                        default:
                            $validator->errors()->add('message', 'Student enrollment ID tidak ada!');
                            break;
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
            'sales_items.*.discount_amount' => 'required',
            'sales_items.*.discount_type' => 'required',

            'tickets.*.barcode_id' => 'required|unique:barcodes|distinct',
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

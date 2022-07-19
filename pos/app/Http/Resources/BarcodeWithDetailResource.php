<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\GateTransaction;
use App\Models\BarcodeType;
use App\Models\Barcode;

class BarcodeWithDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // check if ticket barcode_id is allowed to be modify
        $isAllowChangeBarcode = !GateTransaction::where('barcode_id', '=', $this->barcode_id)->exists();

        // check if the session can be modified
        // technically, if the user has pass gate, the user is not allowed to change session
        $isAllowChangeSession = $isAllowChangeBarcode;

        // but special case for visitor and chaperone
        // where the visitor / chaperone has the ability to extend if the cashier allowed
        // case: his/her child wants to extend session, then he/she is allowed to change session into the next session
        $barcodeSplitArr = preg_split('/(?<=[\sa-zA-Z])(?=[0-9])/', $this->barcode_id);
        if (count($barcodeSplitArr) != 2 && preg_match('/[^A-Za-z]/', $barcodeSplitArr[0])) {
            // wrong format, return false
            $isAllowChangeSession = false;
        } else {
            // format is correct, now check the type
            $prefix = $barcodeSplitArr[0];
            $barcodeType = BarcodeType::find($prefix);

            if ($barcodeType) {
                switch ($barcodeType->type) {
                    case 'VISITOR':
                    case 'CHAPERON':
                        $isAllowChangeSession = true;
                        break;
                }
            }
        }

        // lastly, make sure the created date is current date. 
        // Only allow changes for current date
        $barcode = Barcode::where('barcode_id', '=', $this->barcode_id)->first();
        if ($barcode) {
            if (!($barcode->created_at->isToday())) {
                $isAllowChangeBarcode = false;
                $isAllowChangeSession = false;
            }
        }

        return [
            'id' => $this->id,
            'barcode_id' => $this->barcode_id,
            'activated_by_order_id' => $this->activated_by_order_id,
            'session_name' => $this->session_name,
            'session_day' => $this->session_day,
            'session_start_time' => $this->session_start_time,
            'session_end_time' => $this->session_end_time,
            'is_allow_change_barcode' => $isAllowChangeBarcode,
            'is_allow_change_session' => $isAllowChangeSession,
            'active_on' => (string)$this->active_on,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}

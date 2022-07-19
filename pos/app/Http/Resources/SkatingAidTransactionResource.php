<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\SalesOrder;
use App\Models\SkatingAid;
use App\Models\SkatingAidTransaction;

class SkatingAidTransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $skatingAid = SkatingAid::find($this->skating_aid_id);
        $skating_aid_code = $skatingAid == null ? null : $skatingAid->skating_aid_code;
        
        return [
            'id' => $this->id,
            'sales_order_ref_no' => SalesOrder::where('id', $this->sales_order_id)->first()->order_ref_no,
            'rent_start' => $this->rent_start ? (string) $this->rent_start : null,
            'rent_end' => $this->rent_end ? (string) $this->rent_end : null,
            'skating_aid_id' => $this->skating_aid_id,
            'extended_time' => (int) $this->extended_time,
            'reason' => $this->reason,
            'skating_aid_code' => $skating_aid_code,
            'upgraded' => $this->upgraded,
            'description' => $this->description,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

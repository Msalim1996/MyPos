<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BarcodeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'barcode_id' => $this->barcode_id,
            'sales_order_id' => $this->sales_order_id,
            'session_name' => $this->session_name,
            'session_day' => $this->session_day,
            'session_start_time' => $this->session_start_time,
            'session_end_time' => $this->session_end_time,
            'active_on' => (string)$this->active_on,
            'created_at' => (string)$this->created_at,
            'updated_at' => (string)$this->updated_at,
        ];
    }
}

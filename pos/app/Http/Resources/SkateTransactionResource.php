<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\GateTransaction;

class SkateTransactionResource extends JsonResource
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
            'rent_start' => $this->rent_start ? (string) $this->rent_start : null,
            'rent_end' => $this->rent_end ? (string) $this->rent_end : null,
            'skate_size' => $this->skate_size,
            'username_start' => $this->username_start,
            'username_end' => $this->username_end,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

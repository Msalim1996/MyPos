<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class GateOnGoingTransactionResource extends JsonResource
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
            'barcode_id' => $this->barcode_id
          ];
    }
}

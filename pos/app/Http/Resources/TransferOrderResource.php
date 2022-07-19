<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferOrderResource extends JsonResource
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
            'transfer_ref_no' => $this->transfer_ref_no,
            'status' => $this->status,
            'remark' => $this->remark,
            'ordered_at' => $this->ordered_at,
            'cancelled_at' => $this->cancelled_at,
            'sent_at' => $this->sent_at,
            'received_at' => $this->received_at,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'transfer_items' => TransferItemResource::collection($this->whenLoaded('transferItems'))
        ];
    }
}

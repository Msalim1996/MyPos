<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferItemResource extends JsonResource
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
            'item_id' => $this->item_id,
            'current_location_id' => $this->current_location_id,
            'destination_location_id' => $this->destination_location_id,
            'transfer_order_id' => $this->transfer_order_id,
            'qty' => $this->qty,
            'status' => $this->status,
            'description' => $this->description,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'item' => new ItemResource($this->whenLoaded('item')),
            'current_location' => new LocationResource($this->whenLoaded('currentLocation')),
            'destination_location' => new LocationResource($this->whenLoaded('destinationLocation')),
            'transfer_order' => new TransferOrderResource($this->whenLoaded('transferOrder')),
        ];
    }
}

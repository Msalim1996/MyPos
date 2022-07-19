<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdjustItemResource extends JsonResource
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
            'location_id' => $this->location_id,
            'description' => $this->description,
            'adjust_order_id' => $this->adjust_order_id,
            'old_qty' => $this->old_qty,
            'difference' => $this->difference,
            'status' => $this->status,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'adjust_order' => new AdjustOrderResource($this->whenLoaded('adjustOrder')),
            'item' => new ItemResource($this->whenLoaded('item')),
            'location' => new LocationResource($this->whenLoaded('location'))
        ];
    }
}

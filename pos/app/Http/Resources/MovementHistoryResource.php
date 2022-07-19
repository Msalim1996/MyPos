<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MovementHistoryResource extends JsonResource
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
            'moveable_id' => $this->moveable_id,
            'moveable_type' => $this->moveable_type,
            'original_qty' => $this->original_qty,
            'new_qty' => $this->new_qty,
            'description' => $this->description,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'item' => new ItemResource($this->whenLoaded('item')),
        ];
    }
}

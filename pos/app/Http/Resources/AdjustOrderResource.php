<?php

namespace App\Http\Resources;

use App\Models\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class AdjustOrderResource extends JsonResource
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
            'adjust_ref_no' => $this->adjust_ref_no,
            'ordered_at' => $this->ordered_at,
            'status' => $this->status,
            'remark' => $this->remark,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'adjust_items' => AdjustItemResource::collection($this->whenLoaded('adjustItems'))
        ];
    }
}

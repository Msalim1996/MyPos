<?php

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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
            'description' => $this->description,
            'price' => $this->price,
            'id' => $this->id,
            'name' => $this->name,
            'pre_qty' => $this->pre_qty,
            'pre_item_id' => $this->pre_item_id,
            'pre_item' => new ItemResource($this->whenLoaded('preItem')),
            'pre_type' => $this->pre_type,
            'benefit_qty' => $this->benefit_qty,
            'benefit_item_id' => $this->benefit_item_id,
            'benefit_item' => new ItemResource($this->whenLoaded('benefitItem')),
            'benefit_discount_amount' => $this->benefit_discount_amount,
            'benefit_discount_type' => $this->benefit_discount_type,
            'benefit_type' => $this->benefit_type,
            'apply_multiply' => $this->apply_multiply,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null
        ];
        
    }
}

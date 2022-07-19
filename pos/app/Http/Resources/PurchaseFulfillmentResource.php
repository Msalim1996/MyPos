<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseFulfillmentResource extends JsonResource
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
            'purchase_order_id' => $this->purchase_order_id,
            'purchase_item_id' => $this->purchase_item_id,
            'qty' => $this->qty,
            'description' => $this->description,
            'fulfilled_date' => $this->fulfilled_date,
            'location_id' => $this->location_id,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'purchase_order' => new PurchaseOrderResource($this->whenLoaded('purchaseOrder')),
            'purchase_item' => new PurchaseItemResource($this->whenLoaded('purchaseItem')),
            'location' => new LocationResource($this->whenLoaded('location')),
        ];
    }
}

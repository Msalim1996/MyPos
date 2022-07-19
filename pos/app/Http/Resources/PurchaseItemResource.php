<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseItemResource extends JsonResource
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
            'position_index' => $this->position_index,
            'item_id' => $this->item_id,
            'qty' => $this->qty,
            'unit_price' => $this->unit_price,
            'description' => $this->description,
            'sub_total' =>$this->getSubTotal(),
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
            'item' => new ItemResource($this->whenLoaded('item')),
            'purchase_order' => new PurchaseOrderResource($this->whenLoaded('purchaseOrder')),
            'purchase_fulfillments' => PurchaseFulfillmentResource::collection($this->whenLoaded('purchaseFulfillments'))
        ];
    }
}

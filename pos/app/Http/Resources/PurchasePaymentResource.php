<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchasePaymentResource extends JsonResource
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
            'payment_ref_no' => $this->payment_ref_no,
            'purchase_order_id' => $this->purchase_order_id,
            'payment_method' => $this->payment_method,
            'description' => $this->description,
            'amount' => $this->amount,
            'payment_date' => $this->payment_date,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'purchase_order' => new PurchaseOrderResource($this->whenLoaded('purchaseOrder')),
        ];
    }
}

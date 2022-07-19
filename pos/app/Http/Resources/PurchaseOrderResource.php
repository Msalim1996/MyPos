<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
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
            'purchase_order_ref_no' => $this->purchase_order_ref_no,
            'supplier_id' => $this->supplier_id,
            'location_id' => $this->location_id,
            'fulfillment_status' => $this->fulfillment_status,
            'payment_status' => $this->payment_status,
            'ordered_at' => $this->ordered_at,
            'fulfillment_remark' => $this->fulfillment_remark,
            'payment_remark' => $this->payment_remark,
            'remark' => $this->remark,
            'supplier_address_id' => $this->supplier_address_id,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'purchase_items' => PurchaseItemResource::collection($this->whenLoaded('purchaseItems')),
            'purchase_fulfillments' => PurchaseFulfillmentResource::collection($this->whenLoaded('purchaseFulfillments')),
            'purchase_payments' => PurchasePaymentResource::collection($this->whenLoaded('purchasePayments')),
            'supplier_address' => new SupplierAddressResource(($this->whenLoaded('supplierAddress')))
        ];
    }
}

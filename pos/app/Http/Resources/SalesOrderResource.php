<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesOrderResource extends JsonResource
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
            'order_ref_no' => $this->order_ref_no,
            'ordered_at' => $this->ordered_at,
            'remark' => $this->remark,
            'fulfillment_remark' => $this->fulfillment_remark,
            'return_remark' => $this->return_remark,
            'restock_remark' => $this->restock_remark,
            'payment_remark' => $this->payment_remark,
            'fulfillment_status' => $this->fulfillment_status,
            'payment_status' => $this->payment_status,
            'cancel_reason' => $this->cancel_reason,
            'customer_id' => $this->customer_id,
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'customer_address_id' => $this->customer_address_id,
            'skating_aid_transactions' => SkatingAidTransactionResource::collection($this->whenLoaded('skatingAidTransactions')),
            'customer_address' => new CustomerAddressResource($this->whenLoaded('customerAddress')),
            'sales_items' => SalesItemResource::collection($this->whenLoaded('salesItems')),
            'sales_fulfillments' => SalesFulfillmentResource::collection($this->whenLoaded('salesFulfillments')),
            'sales_returns' => SalesReturnResource::collection($this->whenLoaded('salesReturns')),
            'sales_restocks' => SalesRestockResource::collection($this->whenLoaded('salesRestocks')),
            'sales_payments' => SalesPaymentResource::collection($this->whenLoaded('salesPayments')),
            'sales_order_members' => SalesOrderMemberResource::collection($this->whenLoaded('salesOrderMembers')),
            'tickets' => BarcodeResource::collection($this->whenLoaded('tickets')),
            'location' => new LocationResource($this->whenLoaded('location')),
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

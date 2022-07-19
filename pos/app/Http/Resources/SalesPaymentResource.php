<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesPaymentResource extends JsonResource
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
            'description' => $this->description,
            'payment_date' => $this->payment_date,
            'amount' => $this->amount,
            'type' => $this->type,
            'sales_order_id' => $this->sales_order_id,
            'sales_order' => new SalesOrderResource($this->whenLoaded('salesOrder')),
        ];
    }
}

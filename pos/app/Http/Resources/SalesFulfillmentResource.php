<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesFulfillmentResource extends JsonResource
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
            'description' => $this->description,
            'qty' => $this->qty,
            'fulfilled_date' => $this->fulfilled_date,
            'location_id' => $this->location_id,
            'sales_order_id' => $this->sales_order_id,
            'sales_item_id' => $this->sales_item_id,
            'location' => new LocationResource($this->whenLoaded('location')),
            'sales_order' => new SalesOrderResource($this->whenLoaded('salesOrder')),
            'sales_item' => new SalesItemResource($this->whenLoaded('salesItem')),
        ];
    }
}

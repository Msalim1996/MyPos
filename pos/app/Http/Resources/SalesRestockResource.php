<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesRestockResource extends JsonResource
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
            'restocked_date' => $this->restocked_date,
            'sales_order_id' => $this->sales_order_id,
            'sales_item_id' => $this->sales_item_id,
            'sales_order' => new SalesOrderResource($this->whenLoaded('salesOrder')),
            'sales_item' => new SalesItemResource($this->whenLoaded('salesItem')),
            'location' => new LocationResource($this->whenLoaded('location')),
        ];
    }
}

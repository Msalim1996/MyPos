<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesItemResource extends JsonResource
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
            'position_index' => $this->position_index,
            'description' => $this->description,
            'qty' => $this->qty,
            'tax' => $this->tax,
            'is_pb1' => $this->is_pb1,
            'pb1_qty' => $this->pb1_qty,
            'pb1_tax' => $this->pb1_tax,
            'unit_price' => $this->unit_price,
            'discount_amount' => $this->discount_amount,
            'discount_type' => $this->discount_type,
            'sales_order_id' => $this->sales_order_id,
            'item_id' => $this->item_id,
            'item_type' => $this->item_type,
            'dpp' => $this->dpp,
            'sub_total' =>$this->getSubTotal(),
            'sales_order' => new SalesOrderResource($this->whenLoaded('salesOrder')),
            'sellable' => $this->when(true, function() {
                switch(strtolower($this->item_type)) {
                    case "item":
                        return new ItemResource($this->whenLoaded('item'));
                    case "promotion":
                        return new PromotionResource($this->whenLoaded('promotion'));
                    case "student enrollment":
                        return new StudentEnrollmentResource($this->whenLoaded('studentEnrollment'));
                    default:
                        return null;
                }
            }),
        ];
    }
}

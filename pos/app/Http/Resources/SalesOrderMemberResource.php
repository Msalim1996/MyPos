<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SalesOrderMemberResource extends JsonResource
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
            'sales_order_id' => $this->sales_order_id,
            'member_id' => $this->member_id,
            'member' => new MemberResource($this->whenLoaded('member')),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceTransactionResource extends JsonResource
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
            'date' => $this->date,
            'staff_id' => $this->staff_id,
            'checked_in_on' => $this->checked_in_on,
            'checked_out_on' => $this->checked_out_on,
            'work_type' => $this->work_type,
            'absent_type' => $this->absent_type,
            'is_excluded' => $this->is_excluded,
            'description' => $this->description,
            'pic' => $this->pic,
            'verified_by' => $this->verified_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'staff' => new StaffResource($this->whenLoaded('staff'))
        ];
    }
}

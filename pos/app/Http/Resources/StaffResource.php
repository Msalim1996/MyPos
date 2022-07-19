<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StaffResource extends JsonResource
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
            'enroll_number' => $this->enroll_number,
            'name' => $this->name,
            'position' => $this->position,
            'contract_started_on' => $this->contract_started_on,
            'contract_ended_on' => $this->contract_ended_on,
            'contract_changed_on' => $this->contract_changed_on,
            'bpjs' => $this->bpjs,
            'sp' => $this->sp,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'attendance_transactions' => AttendanceTransactionResource::collection($this->whenLoaded('attendanceTransactions'))
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentAttendanceResource extends JsonResource
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
            'status' => $this->status,
            'remark' => $this->remark,
            'class_schedule_id' => $this->class_schedule_id,
            'member_id' => $this->member_id,
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,

            'class_schedule' => new ClassScheduleResource($this->whenLoaded('classSchedule')),
            'member' => new MemberResource($this->whenLoaded('member'))
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassScheduleResource extends JsonResource
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
            'session_datetime' => $this->session_datetime,
            'duration' => $this->duration,
            'status' => $this->status,
            'student_class_id' => $this->student_class_id,
            'student_class' => new StudentClassResource($this->whenLoaded('studentClass')),
            'student_attendances' => StudentAttendanceResource::collection($this->whenLoaded('studentAttendances')),
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

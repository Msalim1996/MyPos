<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentClassResource extends JsonResource
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
            'class_id' => $this->class_id,
            'age_range' => $this->age_range,
            'date_start' => $this->date_start,
            'date_expired' => $this->date_expired,
            'remark' => $this->remark,
            'level_id' => $this->level_id,
            'level' => new LevelResource($this->whenLoaded('level')),
            'coach_id' => $this->coach_id,
            'coach' => new CoachResource($this->whenLoaded('coach')),
            'course_id' => $this->course_id,
            'course' => new CourseResource($this->whenLoaded('course')),
            'student_enrollments' => StudentEnrollmentResource::collection($this->whenLoaded('studentEnrollments')),
            'class_schedules' => ClassScheduleResource::collection($this->whenLoaded('classSchedules')),
            'cancelled_at' => $this->cancelled_at,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

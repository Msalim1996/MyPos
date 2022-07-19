<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CourseOnGoingClassesResource extends JsonResource
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
            'course_id' => $this->course_id,
            'name' => $this->name,
            'course_type' => $this->course_type,
            'day_type' => $this->day_type,
            'coach_type' => $this->coach_type,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_amount' => $this->discount_amount,
            'price' => $this->price,
            'number_of_students_from' => $this->number_of_students_from,
            'number_of_students_to' => $this->number_of_students_to,
            'number_of_lessons' => $this->number_of_lessons,
            'level_group_id' => $this->level_group_id,
            'level_group' => new LevelGroupResource($this->whenLoaded('levelGroup')),
            'student_classes' => StudentClassResource::collection($this->whenLoaded('studentClasses')->onGoingClasses()),
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

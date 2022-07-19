<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentEnrollmentResource extends JsonResource
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
            'name' => $this->name,
            'price' => $this->price,
            'order_ref_no' => $this->order_ref_no,
            'enrollment_status' => $this->enrollment_status,
            'member_id' => $this->member_id,
            'member' => new MemberResource($this->whenLoaded('member')),
            'student_class_id' => $this->student_class_id,
            'student_class' => new StudentClassResource($this->whenLoaded('studentClass')),
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

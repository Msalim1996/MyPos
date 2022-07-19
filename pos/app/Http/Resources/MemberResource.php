<?php

namespace App\Http\Resources;

use App\Models\Member;
use Illuminate\Http\Resources\Json\JsonResource;

class MemberResource extends JsonResource
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
            'member_id' => $this->member_id,
            'email' => $this->email,
            'name' => $this->name,
            'birthdate' => $this->birthdate ? (string) $this->birthdate : null,
            'gender' => $this->gender,
            'start_date' => $this->start_date ? (string) $this->start_date : null,
            'expiration_date' => $this->expiration_date ? (string) $this->expiration_date : null,
            'address' => $this->address,
            'phone' => $this->phone,
            'remark' => $this->remark,
            'image' => $this->getFirstMediaUrl(Member::$mediaCollectionPath),
            'image_base_64' => $this->when($request->input('base64') == true, function () {
                if ($this->getFirstMedia(Member::$mediaCollectionPath) == null) return "";

                $path   = $this->getFirstMedia(Member::$mediaCollectionPath)->getPath();
                $data   = file_get_contents($path);
                return base64_encode($data);
            }),

            'student_enrollments' => StudentEnrollmentResource::collection($this->whenLoaded('studentEnrollments')),
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

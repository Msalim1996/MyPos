<?php

namespace App\Http\Resources;

use App\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'position' => $this->position,
            'date_join' => $this->date_join,
            'date_left' => $this->date_left,
            'username' => $this->username,
            'starting_position' => $this->starting_position,
            'birthdate' => $this->birthdate,
            'gender' => $this->gender,
            'religion' => $this->religion,
            'address' => $this->address,
            'phone' => $this->phone,
            'remark' => $this->remark,
            'att_id' => $this->att_id,
            'image' => $this->getFirstMediaUrl(User::$mediaCollectionPath),
            'image_base_64' => $this->when($request->input('base64') == true, function () {
                if ($this->getFirstMedia(User::$mediaCollectionPath) == null) return "";

                $path   = $this->getFirstMedia(User::$mediaCollectionPath)->getPath();
                $data   = file_get_contents($path);
                return base64_encode($data);
            }),
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
            'permissions' => PermissionResource::collection($this->whenLoaded('permissions')),
        ];
    }
}

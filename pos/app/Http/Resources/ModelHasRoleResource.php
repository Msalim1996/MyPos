<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Role;
use App\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\RoleResource;

class ModelHasRoleResource extends JsonResource
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
            'model_id' => UserResource::make(User::find($this->model_id)),
            'role_id' => RoleResource::make(Role::find($this->role_id)),
        ];
    }
}

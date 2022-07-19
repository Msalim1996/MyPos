<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Permission;
use App\User;
use App\Http\Resources\UserResource;
use App\Http\Resources\PermissionResource;

class ModelHasPermissionResource extends JsonResource
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
            'permission_id' => PermissionResource::make(Permission::find($this->permission_id)),
            'model_id' => UserResource::make(User::find($this->model_id)),
        ];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\Role;
use App\Models\Permission;
use App\Http\Resources\PermissionResource;
use App\Http\Resources\RoleResource;

class RoleHasPermissionResource extends JsonResource
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
            'permission' => PermissionResource::make(Permission::find($this->permission_id)),
            'role' => RoleResource::make(Role::find($this->role_id)),
        ];
    }
}

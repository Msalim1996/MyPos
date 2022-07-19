<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\PermissionResource;
use App\Models\Permission;

class PermissionListByModelResource extends JsonResource
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
        ];
    }
}

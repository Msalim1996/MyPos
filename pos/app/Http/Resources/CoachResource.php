<?php

namespace App\Http\Resources;

use App\Models\Coach;
use Illuminate\Http\Resources\Json\JsonResource;

class CoachResource extends JsonResource
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
            'coach_id' => $this->coach_id,
            'name' => $this->name,
            'gender' => $this->gender,
            'level' => $this->level,
            'type' => $this->type,
            'category' => $this->category,
            'language' => $this->language,
            'address' => $this->address,
            'phone' => $this->phone,
            'remark' => $this->remark,
            'private_rate' => $this->private_rate,
            'semi_private_rate' => $this->semi_private_rate,
            'group_rate' => $this->group_rate,
            'email' => $this->email,
            'coach_commissions' => CoachCommissionResource::collection($this->whenLoaded('coachCommissions')),
            'image' => $this->getFirstMediaUrl(Coach::$mediaCollectionPath),
            'image_base_64' => $this->when($request->input('base64') == true, function () {
                if ($this->getFirstMedia(Coach::$mediaCollectionPath) == null) return "";

                $path   = $this->getFirstMedia(Coach::$mediaCollectionPath)->getPath();
                $data   = file_get_contents($path);
                return base64_encode($data);
            }),
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

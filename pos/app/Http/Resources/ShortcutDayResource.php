<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ShortcutDayType;
use App\Http\Resources\ShortcutDayTypeResource;

class ShortcutDayResource extends JsonResource
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
            'on_date' => $this->on_date,
            'description' => $this->description,
            'shortcut_day_type_id' => $this->shortcut_day_type_id,
            'shortcut_day_type' => ShortcutDayTypeResource::make($this->whenLoaded('shortcutDayType')),
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

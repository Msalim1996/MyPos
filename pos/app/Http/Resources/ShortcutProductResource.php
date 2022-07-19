<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\ShortcutDayType;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ShortcutDayTypeResource;

class ShortcutProductResource extends JsonResource
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
            'shortcut_key' => $this->shortcut_key,
            'category' => $this->category,
            'shortcut_day_type_id' => $this->shortcut_day_type_id,
            'shortcut_day_type' => ShortcutDayTypeResource::make($this->whenLoaded('shortcutDayType')),
            'item_id' => $this->item_id,
            'item' => ItemResource::make($this->whenLoaded('item')),
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

<?php

namespace App\Http\Resources;

use App\Models\Item;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\File;

class ItemResource extends JsonResource
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
            'sku' => $this->sku,
            'price' => $this->price,
            'purchase_price' => $this->purchase_price != null ? $this->purchase_price : "0", // existing data might be null, therefore need an extra layer
            'type' => $this->type,
            'category' => $this->category,
            'description' => $this->description,
            'uom' => $this->uom,
            'tax' => $this->tax,
            'image' => $this->getFirstMediaUrl(Item::$mediaCollectionPath),
            'image_base_64' => $this->when($request->input('base64') == true, function () {
                if ($this->getFirstMedia(Item::$mediaCollectionPath) == null) return "";

                $path   = $this->getFirstMedia(Item::$mediaCollectionPath)->getPath();
                $data   = file_get_contents($path);
                return base64_encode($data);
            }),
            'member_discount' => new MemberDiscountResource($this->whenLoaded('memberDiscount')),
            'stocks' => StockResource::collection($this->whenLoaded('stocks')),
            'deleted_at' => $this->deleted_at ? (string) $this->deleted_at : null,
            'created_at' => $this->created_at ? (string) $this->created_at : null,
            'updated_at' => $this->updated_at ? (string) $this->updated_at : null,
        ];
    }
}

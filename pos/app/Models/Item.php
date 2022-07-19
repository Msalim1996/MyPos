<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\Models\Media;

class Item extends Model implements Auditable, HasMedia
{
    public static $mediaCollectionPath = "item-images";

    use OwenItAuditable, SoftDeletes, HasMediaTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'sku',
        'price',
        'purchase_price',
        'type',
        'category',
        'description',
        'uom',
        'tax'
    ];

    public function stocks()
    {
        return $this->hasMany('App\Models\Stock', 'item_id', 'id')->withTrashed();
    }

    public function promotionPreItems()
    {
        return $this->hasMany('App\Models\Promotion', 'pre_item_id', 'id');
    }

    public function promotionBenefitItems()
    {
        return $this->hasMany('App\Models\Promotion', 'benefit_item_id', 'id');
    }

    public function memberDiscount()
    {
        return $this->hasOne('App\Models\MemberDiscount', 'item_id', 'id');
    }

    public function salesItems()
    {
        return $this->morphMany('App\Models\SalesItem', 'sellable');
    }

    public function shortcutProducts() 
    {
        return $this->hasMany('App\Models\ShortcutProduct', 'item_id', 'id');
    }
}

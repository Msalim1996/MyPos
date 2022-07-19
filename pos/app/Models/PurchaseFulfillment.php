<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseFulfillment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'purchase_item_id',
        'qty',
        'description',
        'fulfilled_date',
        'location_id'
    ];

    public function purchaseOrder() {
        return $this->belongsTo('App\Models\PurchaseOrder', 'purchase_order_id', 'id');
    }

    public function purchaseItem() {
        return $this->belongsTo('App\Models\PurchaseItem', 'purchase_item_id', 'id');
    }

    public function location() {
        return $this->belongsTo('App\Models\Location', 'location_id', 'id')->withTrashed();
    }

    public function movementHistories() {
        return $this->morphMany('App\Models\MovementHistory','moveable');
    }
}

<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseItem extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'qty',
        'unit_price',
        'description',
        'position_index'
    ];

    public function purchaseFulfillments() {
        return $this->hasMany('App\Models\PurchaseFulfillment', 'purchase_item_id', 'id');
    }

    public function purchaseOrder() {
        return $this->belongsTo('App\Models\PurchaseOrder', 'purchase_order_id', 'id');
    }

    public function item() {
        return $this->belongsTo('App\Models\item', 'item_id', 'id');
    }

    public function getSubTotal()
    {
        return ($this->qty * $this->unit_price);
    }
}

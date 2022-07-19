<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberDiscount extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'item_id',
        'discount_amount',
        'discount_type'
    ];

    public function item() {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }
}

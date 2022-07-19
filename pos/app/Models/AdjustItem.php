<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustItem extends Model
{
    protected $fillable = [
        'adjust_order_id',
        'item_id',
        'location_id',
        'description',
        'status',
        'old_qty',
        'difference'
    ];

    public function item() {
        return $this->belongsTo('App\Models\Item','item_id','id');
    }

    public function location() {
        return $this->belongsTo('App\Models\Location','location_id','id')->withTrashed();
    }

    public function movementHistory() {
        return $this->morphMany('App\Models\MovementHistory','moveable');
    }

    public function adjustOrder() {
        return $this->belongsTo('App\Models\AdjustOrder','adjust_order_id','id');
    }
}

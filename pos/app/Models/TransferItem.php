<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferItem extends Model
{
    protected $fillable = [
        'transfer_order_id',
        'item_id',
        'current_location_id',
        'destination_location_id',
        'description',
        'status',
        'qty'
    ];

    public function transferOrder() {
        return $this->belongsTo('App\Models\TransferOrder','transfer_order_id','id');
    }

    public function item() {
        return $this->belongsTo('App\Models\Item','item_id','id');
    }

    public function currentLocation() {
        return $this->belongsTo('App\Models\Location','current_location_id','id')->withTrashed();
    }

    public function destinationLocation() {
        return $this->belongsTo('App\Models\Location','destination_location_id','id')->withTrashed();
    }

    public function movementHistory() {
        return $this->morphMany('App\Models\MovementHistory','moveable');
    }
}

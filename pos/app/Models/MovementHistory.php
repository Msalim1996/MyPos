<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovementHistory extends Model
{
    protected $fillable = [
        'item_id',
        'moveable_id',
        'moveable_type',
        'original_qty',
        'new_qty',
        'description'
    ];

    public function item() {
        return $this->belongsTo('App\Models\Item','item_id','id');
    }

    public function moveable() {
        return $this->morphTo();
    }
    
}

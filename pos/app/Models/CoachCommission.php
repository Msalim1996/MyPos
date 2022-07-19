<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoachCommission extends Model
{
    protected $fillable = [
        'coach_id',
        'commission_class',
        'commission_percentage'
    ];

    public function coach()
    {
        return $this->belongsTo('App\Models\Coach','coach_id','id');
    }
}

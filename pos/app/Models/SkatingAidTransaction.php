<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkatingAidTransaction extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_ref_no',
        'rent_start',
        'rent_end',
        'skating_aid_id',
        'description',
        'extended_time',
        'reason'
    ];

    protected $casts = [
        'upgraded' => 'boolean'
    ];
}

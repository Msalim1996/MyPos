<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarcodeType extends Model
{
    public $incrementing = false;
    protected $primaryKey = 'prefix';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'prefix', 'type', 'is_allowed_to_rent_shoe'
    ];

    protected $casts = [
        'is_allowed_to_rent_shoe' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkateTransaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'barcode_id', 'rent_start', 'rent_end', 'skate_size'
    ];
}

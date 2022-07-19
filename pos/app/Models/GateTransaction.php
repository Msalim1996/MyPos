<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GateTransaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'barcode_id', 'time_in', 'time_out'
    ];
}

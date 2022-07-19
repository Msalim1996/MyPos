<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GSTimeSchedule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'day', 'start_time', 'end_time'
    ];
}

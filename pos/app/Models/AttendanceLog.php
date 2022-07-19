<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'enroll_number',
        'in_out_mode',
        'date'
    ];
}

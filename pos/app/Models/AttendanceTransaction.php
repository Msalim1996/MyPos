<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceTransaction extends Model
{
    protected $fillable = [
        'date',
        'staff_id',
        'checked_in_on',
        'checked_out_on',
        'work_type',
        'absent_type',
        'is_excluded',
        'description',
        'pic',
        'verified_by'
    ];

    public function staff()
    {
        return $this->belongsTo('App\Models\Staff', 'staff_id', 'id');
    }
}

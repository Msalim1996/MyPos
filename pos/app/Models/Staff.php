<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staffs';
    protected $fillable = [
        'enroll_number',
        'name',
        'position',
        'contract_started_on',
        'contract_ended_on',
        'contract_changed_on',
        'bpjs',
        'sp'
    ];

    public function attendanceTransactions()
    {
        return $this->hasMany('App\Models\AttendanceTransaction','staff_id','id');
    }
}

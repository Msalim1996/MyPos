<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class StudentAttendance extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'status',
        'remark',
        'class_schedule_id',
        'member_id',
    ];

    public function classSchedule() {
        return $this->belongsTo('App\Models\ClassSchedule', 'class_schedule_id', 'id');
    }

    public function member() {
        return $this->belongsTo('App\Models\Member', 'member_id', 'id')->withTrashed();
    }
}



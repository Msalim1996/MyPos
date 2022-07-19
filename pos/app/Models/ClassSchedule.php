<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class ClassSchedule extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'session_datetime',
        'duration',
        'status',
        'student_class_id',
    ];

    public function studentClass() {
        return $this->belongsTo('App\Models\StudentClass', 'student_class_id', 'id');
    }

    public function studentAttendances() {
        return $this->hasMany('App\Models\StudentAttendance', 'class_schedule_id', 'id');
    }
}



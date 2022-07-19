<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class StudentClass extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'class_id',
        'age_range',
        'date_start',
        'date_expired',
        'remark',
        'level_id',
        'coach_id',
        'course_id',
        'cancelled_at'
    ];

    public function level()
    {
        return $this->belongsTo('App\Models\Level', 'level_id', 'id')->withTrashed();
    }

    public function coach()
    {
        return $this->belongsTo('App\Models\Coach', 'coach_id', 'id')->withTrashed();
    }

    public function course()
    {
        return $this->belongsTo('App\Models\Course', 'course_id', 'id')->withTrashed();
    }

    public function classSchedules()
    {
        return $this->hasMany('App\Models\ClassSchedule', 'student_class_id', 'id');
    }

    public function studentEnrollments()
    {
        return $this->hasMany('App\Models\StudentEnrollment', 'student_class_id', 'id');
    }

    public function scopeEnrollmentStatus($query, $inputString)
    {
        switch (strtolower($inputString)) {
            case 'ongoing':
                $query->where('date_start', '<=', Carbon::now())
                ->where('date_expired', '>=', Carbon::now())
                ->where('cancelled_at','=',null);
                break;

            case 'notstarted':
                $query->where('date_start', '>=', Carbon::now());
                break;

            case 'ended':
                $query->where('date_expired', '<=', Carbon::now());
                break;

            case 'cancelled':
                $query->where('cancelled_at', '!=', null);
                break;

            case 'active':
                $query->where('date_expired', '>', Carbon::now())->where('cancelled_at', '=', null);
                break;

            default:
                $query;
                break;
        }
    }
}

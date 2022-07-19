<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Course extends Model implements Auditable
{
    use SoftDeletes, OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'course_id',
        'name',
        'course_type',
        'day_type',
        'coach_type',
        'description',
        'price',
        'number_of_students_from',
        'number_of_students_to',
        'number_of_lessons',
        'level_group_id',
        'discount_amount',
        'discount_type'
    ];

    public function levelGroup() {
        return $this->belongsTo('App\Models\LevelGroup', 'level_group_id', 'id')->withTrashed();
    }

    public function studentClasses() {
        return $this->hasMany('App\Models\StudentClass', 'course_id', 'id');
    }

    public function scopeOnGoingClasses($query)
    {
        return $query->whereHas('studentClasses', function($query) {
            $query->enrollmentStatus('onGoing');
        });
    }
}



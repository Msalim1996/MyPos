<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class StudentEnrollment extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'enrollment_status',
        'member_id',
        'student_class_id',
        'order_ref_no'
    ];

    public function getNameAttribute()
    {
        return $this->member->name . ' - ' . $this->studentClass->class_id
            . ' (' . $this->studentClass->course->name . ')';
    }

    public function getPriceAttribute()
    {
        return $this->studentClass->course->price;
    }

    public function member()
    {
        return $this->belongsTo('App\Models\Member', 'member_id', 'id')->withTrashed();
    }

    public function studentClass()
    {
        return $this->belongsTo('App\Models\StudentClass', 'student_class_id', 'id');
    }

    public function salesItems()
    {
        return $this->morphMany('App\Models\SalesItem', 'sellable');
    }
}

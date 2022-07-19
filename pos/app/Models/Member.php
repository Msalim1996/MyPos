<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Member extends Model implements HasMedia
{
    public static $mediaCollectionPath = "member-images";

    use SoftDeletes, HasMediaTrait;

    protected $fillable = [
        'member_id',
        'email',
        'name',
        'birthdate',
        'gender',
        'start_date',
        'expiration_date',
        'address',
        'phone',
        'remark',
    ];

    public function studentEnrollments() {
        return $this->hasMany('App\Models\StudentEnrollment','member_id','id');
    }
}

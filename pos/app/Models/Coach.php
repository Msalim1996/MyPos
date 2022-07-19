<?php

namespace App\Models;

use App\Traits\UploadTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;

class Coach extends Model implements HasMedia
{
    public static $mediaCollectionPath = "coach-images";
    
    use SoftDeletes, HasMediaTrait;

    protected $fillable = [
        'coach_id',
        'name',
        'gender',
        'level',
        'type',
        'category',
        'language',
        'address',
        'phone',
        'remark',
        'private_rate',
        'semi_private_rate',
        'group_rate'
    ];

    public function coachCommissions()
    {
        return $this->hasMany('App\Models\CoachCommission','coach_id','id');
    }
}

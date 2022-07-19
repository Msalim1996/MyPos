<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class StudentScore extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'score',
        'remark',
        'level_id',
        'member_id',
        'certificate_id',
    ];

    public function level() {
        return $this->belongsTo('App\Models\Level', 'level_id', 'id')->withTrashed();
    }

    public function member() {
        return $this->belongsTo('App\Models\Member', 'member_id', 'id')->withTrashed();
    }

    public function certificate() {
        return $this->belongsTo('App\Models\Certificate', 'certificate_id', 'id');
    }
}



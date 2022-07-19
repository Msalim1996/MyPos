<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModelHasRole extends Model
{
    protected $table = 'model_has_roles';

    public $timestamps = false;
    protected $fillable = [
        'model_id',
        'model_type',
        'role_id',
    ];
}

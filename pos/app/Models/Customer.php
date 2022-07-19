<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Customer extends Model implements Auditable
{
    use OwenItAuditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'fax',
        'phone',
        'email',
        'website',
        'remark',
    ];

    public function customerAddresses() {
        return $this->hasMany('App\Models\CustomerAddress', 'customer_id', 'id');
    }
}

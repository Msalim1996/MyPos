<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class CustomerAddress extends Model implements Auditable
{
    use OwenItAuditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'street',
        'city',
        'state',
        'zip',
        'country',
        'remark',
        'type',
        'default_billing_address',
        'default_shipping_address',
        'customer_id'
    ];

    public function customer() {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id');
    }
}

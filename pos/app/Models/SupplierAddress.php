<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplierAddress extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'supplier_id',
        'name',
        'street',
        'city',
        'state',
        'zip',
        'country',
        'remark',
        'type',
        'default_billing_address',
        'default_shipping_address'
    ];

    public function supplier() {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id');
    }
}

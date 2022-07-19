<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'website',
        'fax',
        'email',
        'remark'
    ];

    public function supplierAddresses() {
        return $this->hasMany('App\Models\SupplierAddress','supplier_id','id');
    }

    public function purchaseOrders() {
        return $this->hasMany('App\Models\PurchaseOrder','supplier_id','id');
    }
}

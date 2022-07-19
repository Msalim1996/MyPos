<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchasePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'payment_ref_no',
        'purchase_order_id',
        'payment_method',
        'description',
        'amount',
        'payment_date'
    ];

    public function purchaseOrder() {
        return $this->belongsTo('App\Models\PurchaseOrder', 'purchase_order_id', 'id');
    }
}

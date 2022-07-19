<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'purchase_order_ref_no',
        'supplier_address_id',
        'supplier_id',
        'location_id',
        'fulfillment_status',
        'payment_status',
        'ordered_at',
        'remark',
        'fulfillment_remark',
        'payment_remark'
    ];

    public function supplier() {
        return $this->belongsTo('App\Models\Supplier', 'supplier_id', 'id')->withTrashed();
    }

    public function location() {
        return $this->belongsTo('App\Models\Location', 'location_id', 'id')->withTrashed();
    }

    public function purchaseFulfillments() {
        return $this->hasMany('App\Models\PurchaseFulfillment', 'purchase_order_id', 'id');
    }

    public function purchaseItems() {
        return $this->hasMany('App\Models\PurchaseItem', 'purchase_order_id', 'id');
    }

    public function purchasePayments() {
        return $this->hasMany('App\Models\PurchasePayment', 'purchase_order_id', 'id');
    }

    public function supplierAddress() {
        return $this->belongsTo('App\Models\SupplierAddress','supplier_address_id','id')->withTrashed();
    }
    
    /**
     * $boolean = true, will return paid sales order, false otherwise
     */
    public function scopePaid($query, $boolean)
    {
        if ($boolean) {
            $query->where('payment_status', '=', 'Paid');
        } else {
            $query->where('payment_status', '!=', 'Paid');
        }
    }

    /**
     * $boolean = true, will return fulfilled sales order, false otherwise
     */
    public function scopeFulfilled($query, $boolean)
    {
        if ($boolean) {
            $query->where('fulfillment_status', '=', 'Fulfilled');
        } else {
            $query->where('fulfillment_status', '!=', 'Fulfilled');
        }
    }

    /**
     * $boolean = true, will return fulfilled sales order, false otherwise
     */
    public function scopeCompleted($query, $boolean)
    {
        if ($boolean) {
            $query->where('fulfillment_status', '=', 'Fulfilled')->where('payment_status', '=', 'Paid');
        } else {
            $query->where('fulfillment_status', '!=', 'Fulfilled')->orWhere('payment_status', '!=', 'Paid');
        }
    }
}

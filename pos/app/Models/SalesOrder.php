<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class SalesOrder extends Model implements Auditable
{
    use OwenItAuditable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_ref_no',
        'ordered_at',
        'remark',
        'fulfillment_remark',
        'return_remark',
        'restock_remark',
        'payment_remark',
        'cancel_reason',
        'fulfillment_status',
        'payment_status',
        'customer_id',
        'location_id',
        'customer_address_id'
    ];

    public function customer()
    {
        return $this->belongsTo('App\Models\Customer', 'customer_id', 'id')->withTrashed();
    }

    public function customerAddress()
    {
        return $this->belongsTo('App\Models\CustomerAddress', 'customer_address_id', 'id')->withTrashed();
    }

    public function salesOrderMembers()
    {
        return $this->hasMany('App\Models\SalesOrderMember', 'sales_order_id', 'id');
    }

    public function salesItems()
    {
        return $this->hasMany('App\Models\SalesItem', 'sales_order_id', 'id');
    }

    public function salesFulfillments()
    {
        return $this->hasMany('App\Models\SalesFulfillment', 'sales_order_id', 'id');
    }

    public function salesReturns()
    {
        return $this->hasMany('App\Models\SalesReturn', 'sales_order_id', 'id');
    }

    public function salesRestocks()
    {
        return $this->hasMany('App\Models\SalesRestock', 'sales_order_id', 'id');
    }

    public function salesPayments()
    {
        return $this->hasMany('App\Models\SalesPayment', 'sales_order_id', 'id');
    }

    public function tickets()
    {
        return $this->hasMany('App\Models\Barcode', 'sales_order_id', 'id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id', 'id')->withTrashed();
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

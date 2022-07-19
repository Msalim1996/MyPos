<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class SalesPayment extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_ref_no',
        'description',
        'payment_date',
        'amount',
        'type',
        'sales_order_id'
    ];

    public function salesOrder() {
        return $this->belongsTo('App\Models\SalesOrder', 'sales_order_id', 'id');
    }
}

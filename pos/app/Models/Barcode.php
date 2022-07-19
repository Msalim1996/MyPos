<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Barcode extends Model
{
    protected $casts = [
        'active_on' => 'datetime'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'barcode_id', 'sales_order_id', 'active_on', 'session_name', 'session_day', 'session_start_time', 'session_end_time'
    ];

    public function salesOrder() {
        return $this->belongsTo('App\Models\SalesOrder', 'id', 'sales_order_id');
    }
}

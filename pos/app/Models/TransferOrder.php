<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransferOrder extends Model
{
    protected $fillable = [
        'transfer_ref_no',
        'remark',
        'status',
        'ordered_at',
        'cancelled_at',
        'sent_at',
        'received_at'
    ];

    public function transferItems() {
        return $this->hasMany('App\Models\TransferItem','transfer_order_id','id');
    }
}

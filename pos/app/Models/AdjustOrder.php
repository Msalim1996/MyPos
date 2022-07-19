<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdjustOrder extends Model
{
    protected $fillable = [
        'ordered_at',
        'adjust_ref_no',
        'remark',
        'status'
    ];

    public function adjustItems() {
        return $this->hasMany('App\Models\AdjustItem','adjust_order_id','id');
    }

    /**
     * $boolean = true, will return completed adjust order, false otherwise
     */
    public function scopeStatus($query, $boolean)
    {
        if ($boolean) {
            $query->where('status', '=', 'Completed');
        } else {
            $query->where('status', '!=', 'Completed');
        }
    }
}

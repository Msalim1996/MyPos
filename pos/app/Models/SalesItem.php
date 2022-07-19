<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class SalesItem extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'position_index',
        'description',
        'qty',
        'unit_price',
        'discount_amount',
        'discount_type',
        'sales_order_id',
        'item_id',
        'item_type',
        'tax',
        'dpp',
        'pb1_tax',
        'pb1_dpp',
        'is_pb1'
    ];

    public function salesOrder()
    {
        return $this->belongsTo('App\Models\SalesOrder', 'sales_order_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function promotion()
    {
        return $this->belongsTo('App\Models\Promotion', 'item_id', 'id');
    }

    public function studentEnrollment()
    {
        return $this->belongsTo('App\Models\StudentEnrollment', 'item_id', 'id');
    }

    public function getSubTotal()
    {
        switch (strtolower($this->discount_type)) {
            case strtolower(DiscountType::percentage()):
                return (($this->qty * $this->unit_price) * (1 - ($this->discount_amount / 100)) + $this->tax);
            case strtolower(DiscountType::amount()):
                return (($this->qty * $this->unit_price) - $this->discount_amount + $this->tax);
            default:
                return (($this->qty * $this->unit_price) + $this->tax);
        }
    }

    public function sellable()
    {
        return $this->morphTo();
    }
}
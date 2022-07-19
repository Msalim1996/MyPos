<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class SalesFulfillment extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'description',
        'qty',
        'fulfilled_date',
        'location_id',
        'sales_order_id',
        'sales_item_id',
    ];

    public function location()
    {
        return $this->belongsTo('App\Models\Location', 'location_id', 'id')->withTrashed();
    }

    public function salesOrder()
    {
        return $this->belongsTo('App\Models\SalesOrder', 'sales_order_id', 'id');
    }

    public function salesItem()
    {
        return $this->belongsTo('App\Models\SalesItem', 'sales_item_id', 'id');
    }

    public function movementHistories() {
        return $this->morphMany('App\Models\MovementHistory','moveable');
    }
}

<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class SalesOrderMember extends Model implements Auditable
{
    use OwenItAuditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sales_order_id',
        'member_id',
    ];

    public function salesOrder()
    {
        return $this->belongsTo('App\Models\SalesOrder', 'sales_order_id', 'id');
    }

    public function member()
    {
        return $this->belongsTo('App\Models\Member', 'member_id', 'id')->withTrashed();
    }
}

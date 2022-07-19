<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as OwenItAuditable;
use OwenIt\Auditing\Contracts\Auditable;

class Stock extends Model implements Auditable
{
    use OwenItAuditable, SoftDeletes;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'qty', 'item_id', 'location_id'
    ];

    public function item()
    { 
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }

    public function location()
    { 
        return $this->belongsTo('App\Models\Location', 'location_id', 'id')->withTrashed();
    }
}

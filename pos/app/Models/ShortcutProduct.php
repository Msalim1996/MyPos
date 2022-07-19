<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortcutProduct extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'shortcut_key', 'position_index', 'shortcut_day_type_id', 'item_id', 'category'
    ];

    public function shortcutDayType()
    {
        return $this->belongsTo('App\Models\ShortcutDayType', 'shortcut_day_type_id', 'id');
    }

    public function item()
    {
        return $this->belongsTo('App\Models\Item', 'item_id', 'id');
    }
}

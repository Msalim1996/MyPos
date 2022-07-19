<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortcutDay extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'on_date', 'shortcut_day_type_id', 'description'
    ];

    public function shortcutDayType()
    {
        return $this->belongsTo('App\Models\ShortcutDayType', 'shortcut_day_type_id');
    }
}

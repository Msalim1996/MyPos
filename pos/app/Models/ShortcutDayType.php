<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortcutDayType extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public function shortcutDays()
    {
        return $this->hasMany('App\Models\ShortcutDay', 'shortcut_day_type_id', 'id');
    }

    public function shortcutProducts()
    {
        return $this->hasMany('App\Models\ShortcutProduct', 'shortcut_day_type_id', 'id');
    }
}

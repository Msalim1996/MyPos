<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests\ShortcutDayTypeStoreRequest as StoreRequest;
use App\Http\Requests\ShortcutDayTypeUpdateRequest as UpdateRequest;
use App\Http\Resources\ShortcutDayTypeResource;
use App\Http\Resources\ShortcutProductResource;
use App\Models\ShortcutDay;
use App\Models\ShortcutDayType;
use App\Models\ShortcutProduct;
use Carbon\Carbon;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Product Shortcut Functionality
 *
 * @group Shortcut CRUD & functionality
 */
class ShortcutProductController extends Controller
{
    /**
     * Get today product / item shortcuts
     * 
     * @authenticated
     */
    public function getTodayItems() {
        return $this->getItemsByDate(Carbon::now());
    }

    /**
     * Get product / item shortcuts based on the given date
     */
    public function getItemsByDate(Carbon $date) {
        // if date is found in shortcut day, then get the day type and return the list of order items
        $shortcutDay = ShortcutDay::where('on_date', '=', $date->toDateString())->first();
        if ($shortcutDay) {
            return ShortcutProductResource::collection(ShortcutProduct::with('item', 'item.memberDiscount')->where('shortcut_day_type_id', '=', $shortcutDay->shortcut_day_type_id)->get());
        }

        // if date is not found in shortcut day, then check if it is weekend or weekday
        if ($date->isWeekday()) {
            // look for weekday order items
            $shortcutDayType = ShortcutDayType::where('name', '=', 'Weekday')->first();
            return ShortcutProductResource::collection(ShortcutProduct::with('item', 'item.memberDiscount')->where('shortcut_day_type_id', '=', $shortcutDayType->id)->get());
        } else {
            // look for weekend order items
            $shortcutDayType = ShortcutDayType::where('name', '=', 'Weekend')->first();
            return ShortcutProductResource::collection(ShortcutProduct::with('item', 'item.memberDiscount')->where('shortcut_day_type_id', '=', $shortcutDayType->id)->get());
        }
    }
}

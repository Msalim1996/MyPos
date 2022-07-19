<?php

namespace App\Utils;

use App\Models\DbNumberCounter;
use Carbon\Carbon;

class GetAutoNumber
{
    /**
     * The way how it works:
     * basically it will check the year-month-day to get the next number
     * if today date is the same with the db counter column, then the number will be incremented
     * if today date is not the same, then update the db row data and reset the number into 1
     * 
     * example:
     * db contain => SO, 2019, 01, null, 1
     *      if current date time in the range of month 01, then number is incremented, otherwise, reset into 0
     * db contain => SO, 2019, 01, 01, 1
     *      if current date time in the day of 01, then the number is incremented, otherwise, reset into 0
     * 
     * @param $type the type of auto number
     */
    public static function getNextNumber($type)
    {
        $dbNumberCounter = DbNumberCounter::where('type', '=', $type)->firstOrFail();
        $now = Carbon::now();

        if ($dbNumberCounter->year != null && $dbNumberCounter->year != $now->year) self::resetCounter($dbNumberCounter, $now);
        if ($dbNumberCounter->month != null && $dbNumberCounter->month != $now->month) self::resetCounter($dbNumberCounter, $now);
        if ($dbNumberCounter->day != null && $dbNumberCounter->day != $now->day) self::resetCounter($dbNumberCounter, $now);

        $autoNumberString = $dbNumberCounter->type
                . ($dbNumberCounter->year != null ? '/' . $dbNumberCounter->year : '')
                . ($dbNumberCounter->month != null ? '/' . $dbNumberCounter->month : '')
                . ($dbNumberCounter->day != null ? '/' . $dbNumberCounter->day : '')
                . '/' . $dbNumberCounter->number;
                
        $dbNumberCounter->number += 1;
        $dbNumberCounter->save();

        return $autoNumberString;
    }

    /**
     * Reset counter to start with 0
     */
    private static function resetCounter($dbNumberCounter, $now) {
        if ($dbNumberCounter->year != null) $dbNumberCounter->year = $now->year;
        if ($dbNumberCounter->month != null) $dbNumberCounter->month = $now->month;
        if ($dbNumberCounter->day != null) $dbNumberCounter->day = $now->day;
        $dbNumberCounter->number = 1;
    }
}

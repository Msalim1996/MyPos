<?php

namespace App\Utils;

use App\Models\DbNumberCounter;
use Carbon\Carbon;

class GetAutoIncrementMember
{
    /**
     * The way how it works:
     * how it works is add the type from db_number_counters, combine it with number
     * , and add it with str_pad with '0' to the left
     * 
     * @param $type the type of auto number
     */
    public static function getNextNumber($type)
    {
        $dbNumber = DbNumberCounter::where('type', '=', $type)->firstOrFail();

        $autoNumberString = $dbNumber->type . str_pad($dbNumber->number, 4, '0', STR_PAD_LEFT);
        $dbNumber->number += 1;
        $dbNumber->save();

        return $autoNumberString;
    }
}

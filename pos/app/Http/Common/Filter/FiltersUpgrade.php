<?php

namespace App\Http\Common\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * Filter upgrade allow null, true and false as the input
 * NOTE: to filter by true, pass value "1"
 * 
 * example filter[upgraded]=Null,0,1
 * example filter[upgraded]=0,1
 * example filter[upgraded]=1
 */
class FiltersUpgrade implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $filteredValues = $this->changeInputIntoArray($value);
        // Get all query which is cancelled, whatever the upgraded is
        if (in_array("Null", $filteredValues)) {
            $filteredValues = array_diff($filteredValues, ["Null"]);
            return $query->where(function($query) use ($filteredValues) {
                $query->whereNull('upgraded')->orWhereIn('upgraded',$filteredValues);
            });
        }

        // Get all query base on the given upgraded (exclude cancelled)
        if (!empty($filteredValues)) {
            return $query->whereNotNull('upgraded')->whereIn('upgraded',$filteredValues);
        }

        // if empty string is given, then return all the data
        return $query;
    }

    private function changeInputIntoArray($input)
    {
        if ($input == "") return [];
        if (is_string($input)) return [$input];
        return $input;
    }
}
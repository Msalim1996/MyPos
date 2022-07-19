<?php

namespace App\Http\Common\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class FiltersStatus implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $filteredValues = $this->changeInputIntoArray($value);
        // Get all query which is cancelled, whatever the status is
        if (in_array("Cancelled", $filteredValues)) {
            $filteredValues = array_diff($filteredValues, ["Cancelled"]);
            return $query->where(function($query) use ($filteredValues) {
                $query->where('cancelled_at', '!=', null)->orWhereIn('status',$filteredValues);
            });
        }

        // Get all query base on the given status (exclude cancelled)
        if (!empty($filteredValues)) {
            return $query->where('cancelled_at', '=', null)->whereIn('status',$filteredValues);
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

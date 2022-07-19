<?php

namespace App\Http\Common\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersMoveableType implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        $filteredValues = $this->changeInputIntoArray($value);
        foreach($filteredValues as $value)
        {
            $queryValue = $query->orWhere('moveable_type','=',$value);
        }
        return $queryValue;
    }

    private function changeInputIntoArray($input)
    {
        if ($input == "") return [];
        if (is_string($input)) return [$input];
        return $input;
    }
}

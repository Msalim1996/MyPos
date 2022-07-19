<?php

namespace App\Http\Common\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersShiftStartRange implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->whereBetween('started_on',$value);
    }
}

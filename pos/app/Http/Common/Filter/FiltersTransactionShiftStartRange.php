<?php

namespace App\Http\Common\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersTransactionShiftStartRange implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->whereBetween('shift_started_on',$value);
    }
}

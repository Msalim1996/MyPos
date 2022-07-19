<?php

namespace App\Http\Common\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersTransactionCheckedInRange implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        return $query->whereBetween('checked_in_on',$value);
    }
}

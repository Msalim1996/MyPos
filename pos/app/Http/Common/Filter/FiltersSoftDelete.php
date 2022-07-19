<?php

namespace App\Http\Common\Filter;

use Spatie\QueryBuilder\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

class FiltersSoftDelete implements Filter
{

    public function __invoke(Builder $query, $value, string $property): Builder
    {
        if (strtolower($value) == Status::ALL) {
            $query->withTrashed();
        } else if (strtolower($value) == Status::INACTIVE) {
            $query->onlyTrashed();
        } else {
            // no changes in query if looking for 'ACTIVE' status
        }

        return $query;
    }
}

class Status
{
    const ALL = 'all';
    const ACTIVE = 'active';
    const INACTIVE = 'inactive';
}

<?php

namespace App\Http\Controllers\PublicAPI;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PublicLocationController extends Controller
{
    public function index()
    {
        $locations = QueryBuilder::for(Location::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->get();
        return LocationResource::collection($locations);
    }
}

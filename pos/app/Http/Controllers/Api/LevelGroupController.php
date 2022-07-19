<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LevelGroupRequest as StoreRequest;
use App\Http\Requests\LevelGroupRequest as UpdateRequest;
use App\Http\Resources\LevelGroupResource;
use App\Models\LevelGroup;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group Location CRUD
 */
class LevelGroupController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * GET all
     * 
     * @authenticated
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $levelGroups = QueryBuilder::for(LevelGroup::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->get();
        return LevelGroupResource::collection($levelGroups);
    }

    /**
     * POST
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $levelGroup = LevelGroup::create([
            'name' => $request->name,
        ]);

        return new LevelGroupResource($levelGroup);
    }

    /**
     * GET
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = LevelGroup::withTrashed()->where('id', $id);
        $levelGroup = QueryBuilder::for($query)
            ->firstOrFail();
        return new LevelGroupResource($levelGroup);
    }

    /**
     * PUT
     * 
     * @authenticated
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, LevelGroup $levelGroup)
    {
        $levelGroup->update($request->only(['name']));

        return new LevelGroupResource($levelGroup);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(LevelGroup $levelGroup)
    {
        $levelGroup->delete();

        return response()->json(null, 204);
    }

    /**
     * Restore
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function restore(Request $request, int $id)
    {
        $levelGroup = LevelGroup::withTrashed()->findOrFail($id);
        if ($levelGroup->trashed()) $levelGroup->restore();

        return new LevelGroupResource($levelGroup);
    }
}

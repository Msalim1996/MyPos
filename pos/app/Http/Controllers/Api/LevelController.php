<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\LevelRequest as StoreRequest;
use App\Http\Requests\LevelRequest as UpdateRequest;
use App\Http\Resources\LevelResource;
use App\Models\Level;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @group Location CRUD
 */
class LevelController extends Controller
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
        $levels = QueryBuilder::for(Level::class)
            ->allowedFilters([
                AllowedFilter::custom('status', new FiltersSoftDelete),
            ])
            ->allowedIncludes(['level_group'])
            ->get();
        return LevelResource::collection($levels);
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
        $level = Level::create([
            'name' => $request->name,
            'level_group_id' => $request->level_group_id,
        ]);

        return new LevelResource($level);
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
        $query = Level::withTrashed()->where('id', $id);
        $level = QueryBuilder::for($query)
            ->allowedIncludes(['level_group'])
            ->firstOrFail();
        return new LevelResource($level);
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
    public function update(UpdateRequest $request, Level $level)
    {
        $level->update($request->only(['name', 'level_group_id']));

        return new LevelResource($level);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Level $level)
    {
        $level->delete();

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
        $level = Level::withTrashed()->findOrFail($id);
        if ($level->trashed()) $level->restore();

        return new LevelResource($level);
    }
}

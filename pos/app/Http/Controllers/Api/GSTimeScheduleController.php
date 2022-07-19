<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GSTimeScheduleResource;
use App\Models\GSTimeSchedule;

use App\Http\Requests\GSTimeScheduleRequest as StoreRequest;
use App\Http\Requests\GSTimeScheduleRequest as UpdateRequest;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Time Schedule CRUD & functionality
 */
class GSTimeScheduleController extends Controller
{
    /**
     * GET all
     * 
     * @authenticated
     * 
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $timeSchedules = QueryBuilder::for(GSTimeSchedule::class)
            ->allowedFilters([
                'day'
            ])->get();
        return GSTimeScheduleResource::collection($timeSchedules);
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
        $gsTimeSchedule = GSTimeSchedule::create([
            'name' => $request->name,
            'day' => $request->day,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        return new GSTimeScheduleResource($gsTimeSchedule);
    }

    /**
     * GET
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(GSTimeSchedule $gsTimeSchedule)
    {
        return new GSTimeScheduleResource($gsTimeSchedule);
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
    public function update(UpdateRequest $request, GSTimeSchedule $gsTimeSchedule)
    {
        $gsTimeSchedule->update($request->only(['name', 'day', 'start_time', 'end_time']));

        return new GSTimeScheduleResource($gsTimeSchedule);
    }

    /**
     * DELETE
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(GSTimeSchedule $gsTimeSchedule)
    {
        $gsTimeSchedule->delete();

        return response()->json(null, 204);
    }
}

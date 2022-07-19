<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CoachCommissionResource;
use App\Http\Requests\CoachCommissionStoreRequest as StoreRequest;
use App\Http\Requests\CoachCommissionUpdateRequest as UpdateRequest;
use App\Http\Resources\CoachResource;
use App\Models\CoachCommission;
use Spatie\QueryBuilder\QueryBuilder;

class CoachCommissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $coachCommissions = QueryBuilder::for(CoachCommission::class)
            ->allowedIncludes(['coach'])
            ->get();
        return CoachResource::collection($coachCommissions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $coachCommission = CoachCommission::create([
            'coach_id' => $request->input('coach_id'),
            'commission_percentage' => $request->input('commission_percentage'),
            'commission_class' => $request->input('commission_class')
        ]);

        return new CoachCommissionResource($coachCommission->load(['coach']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = CoachCommission::where('id', $id);
        $coachCommission = QueryBuilder::for($query)
            ->allowedIncludes(['coach'])
            ->firstOrFail();
        return new CoachCommissionResource($coachCommission);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, CoachCommission $coachCommission)
    {
        $coachCommission = CoachCommission::update([
            'coach_id' => $request->input('coach_id'),
            'commission_percentage' => $request->input('commission_percentage'),
            'commission_class' => $request->input('commission_class')
        ]);

        return new CoachCommissionResource($coachCommission->load(['coach']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(CoachCommission $coachCommission)
    {
        $coachCommission->delete();

        return response()->json(null, 204);
    }
}

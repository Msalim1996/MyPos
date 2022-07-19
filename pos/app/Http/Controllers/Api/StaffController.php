<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Requests\StaffStoreRequest as StoreRequest;
use App\Http\Requests\StaffUpdateRequest as UpdateRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\StaffResource;
use App\Models\Staff;
use Spatie\QueryBuilder\QueryBuilder;

class StaffController extends Controller
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
        $staffs = QueryBuilder::for(Staff::class)
                    ->allowedIncludes(['attendance_transactions'])
                    ->get();

        return StaffResource::collection($staffs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $staff = Staff::create([
            'enroll_number' => $request->enroll_number,
            'name' => $request->name,
            'position' => $request->position,
            'contract_started_on' => $request->contract_started_on,
            'contract_ended_on' => $request->contract_ended_on,
            'contract_changed_on' => $request->contract_changed_on,
            'bpjs' => $request->bpjs,
            'sp' => $request->sp
        ]);

        return new StaffResource($staff->load(['attendanceTransactions']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = Staff::where('id', $id);
        $staff = QueryBuilder::for($query)
                ->allowedIncludes(['attendance_transactions'])
                ->firstOrFail();
        return new StaffResource($staff);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Staff $staff)
    {
        $staff->update([
            'enroll_number' => $request->enroll_number,
            'name' => $request->name,
            'position' => $request->position,
            'contract_started_on' => $request->contract_started_on,
            'contract_ended_on' => $request->contract_ended_on,
            'contract_changed_on' => $request->contract_changed_on,
            'bpjs' => $request->bpjs,
            'sp' => $request->sp
        ]);

        return new StaffResource($staff->load(['attendanceTransactions']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Staff $staff)
    {
        $attendanceTransactions = $staff->attendanceTransactions;
        foreach($attendanceTransactions as $attendanceTransaction)
            $attendanceTransaction->delete();
        
        $staff->delete();

        return response()->json(null, 204);
    }
}

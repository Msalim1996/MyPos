<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersDateRangeDateOfTransaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceTransactionResource;
use App\Models\AttendanceTransaction;
use App\Http\Requests\AttendanceTransactionStoreRequest as StoreRequest;
use App\Http\Requests\AttendanceTransactionUpdateRequest as UpdateRequest;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttendanceTransactionController extends Controller
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
        $attendanceTransactions = QueryBuilder::for(AttendanceTransaction::class)
                                ->allowedFilters([
                                    AllowedFilter::custom('start-between', new FiltersDateRangeDateOfTransaction),
                                    'staff_id'
                                ])
                                ->allowedIncludes(['staff'])
                                ->get();

        return AttendanceTransactionResource::collection($attendanceTransactions);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $attendanceTransaction = AttendanceTransaction::create([
            'date' => $request->date,
            'staff_id' => $request->staff_id,
            'checked_in_on' => $request->checked_in_on,
            'checked_out_on' => $request->checked_out_on,
            'work_type' => $request->work_type,
            'absent_type' => $request->absent_type,
            'is_excluded' => $request->is_excluded,
            'description' => $request->description,
            'pic' => $request->pic,
            'verified_by' => $request->verified_by,
        ]);

        return new AttendanceTransactionResource($attendanceTransaction->load(['staff']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = AttendanceTransaction::where('id', $id);
        $attendanceTransaction = QueryBuilder::for($query)
            ->allowedIncludes(['staff'])
            ->firstOrFail();
        return new AttendanceTransactionResource($attendanceTransaction);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AttendanceTransaction $attendanceTransaction)
    {
        $attendanceTransaction->update([
            'date' => $request->date,
            'staff_id' => $request->staff_id,
            'checked_in_on' => $request->checked_in_on,
            'checked_out_on' => $request->checked_out_on,
            'work_type' => $request->work_type,
            'absent_type' => $request->absent_type,
            'is_excluded' => $request->is_excluded,
            'description' => $request->description,
            'pic' => $request->pic,
            'verified_by' => $request->verified_by,
        ]);

        return new AttendanceTransactionResource($attendanceTransaction->load(['staff']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttendanceTransaction $attendanceTransaction)
    {
        $attendanceTransaction->delete();

        return response()->json(null, 204);
    }
}

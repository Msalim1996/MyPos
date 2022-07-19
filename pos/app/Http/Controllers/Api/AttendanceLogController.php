<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceLogResource;
use App\Models\AttendanceLog;
use App\Http\Requests\AttendanceLogStoreRequest as StoreRequest;
use App\Http\Requests\AttendanceLogStoreListRequest as StoreListRequest;
use App\Http\Requests\AttendanceLogUpdateRequest as UpdateRequest;
use App\Models\AttendanceTransaction;
use App\Models\Staff;
use Spatie\QueryBuilder\QueryBuilder;

class AttendanceLogController extends Controller
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
        $attendanceLogs = QueryBuilder::for(AttendanceLog::class)->get();

        return AttendanceLogResource::collection($attendanceLogs);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $attendanceLog = AttendanceLog::create([
            'enroll_number' => $request->enroll_number,
            'in_out_mode' => $request->in_out_mode,
            'date' => $request->date
        ]);

        return new AttendanceLogResource($attendanceLog);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $query = AttendanceLog::where('id', $id);
        $attendanceLog = QueryBuilder::for($query)
            ->firstOrFail();
        return new AttendanceLogResource($attendanceLog);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, AttendanceLog $attendanceLog)
    {
        $attendanceLog->update([
            'enroll_number' => $request->enroll_number,
            'in_out_mode' => $request->in_out_mode,
            'date' => $request->date
        ]);

        return new AttendanceLogResource($attendanceLog);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(AttendanceLog $attendanceLog)
    {
        $attendanceLog->delete();

        return response()->json(null, 204);
    }

    // TODO: ADD SPAM REMOVAL AND ATTENDANCE TRANSACTION CREATION LOGIC HERE
    public function addListOfAttendanceLogs(StoreListRequest $request) 
    {
        if ($request->attendance_logs) {
            $attendanceLogs = [];
            for ($index = 0; $index < count($request->attendance_logs); $index++) {
                $enrollNumber = $request->input('attendance_logs.' . $index . '.enroll_number');
                $fullDate = $request->input('attendance_logs.' . $index . '.date');
                $date = date("Y-m-d", strtotime($fullDate));;
                $inOutMode = $request->input('attendance_logs.' . $index . '.in_out_mode');

                if (count($attendanceLogs) == 0)
                    array_push($attendanceLogs, array("enroll_number" => $enrollNumber, "in_out_mode" => $inOutMode, "date" => $fullDate));
                else {
                    $foundLogs = [];
                    foreach ($attendanceLogs as $attendanceLog)
                        if ($attendanceLog["enroll_number"] == $enrollNumber && date("Y-m-d", strtotime($attendanceLog["date"])) == $date)
                            array_push($foundLogs, array("enroll_number" => $attendanceLog["enroll_number"], "in_out_mode" => $attendanceLog["in_out_mode"], "date" => date("Y-m-d", strtotime($attendanceLog["date"]))));
                    if (count($foundLogs) == 0)
                        array_push($attendanceLogs, array("enroll_number" => $enrollNumber, "in_out_mode" => $inOutMode, "date" => $fullDate));
                    else {
                        $lastLog = end($foundLogs);
                        if ($lastLog["enroll_number"] == $enrollNumber && $lastLog["date"] == $date)
                            if ($lastLog["in_out_mode"] != $inOutMode)
                                array_push($attendanceLogs, array("enroll_number" => $enrollNumber, "in_out_mode" => $inOutMode, "date" => $fullDate));
                    }
                }
            }
        
            $processedAttendanceLogs = [];
            foreach ($attendanceLogs as $attendanceLog) {
                $processingAttendanceLog = AttendanceLog::create([
                    'enroll_number' => $attendanceLog["enroll_number"],
                    'in_out_mode' => $attendanceLog["in_out_mode"],
                    'date' => $attendanceLog["date"]
                ]);
                array_push($processedAttendanceLogs, $processingAttendanceLog);
            }
        }

        foreach ($processedAttendanceLogs as $attendanceLog) {
            $staff = Staff::where('enroll_number', $attendanceLog->enroll_number)->first();
            $date = date("Y-m-d", strtotime($attendanceLog->date));
            $time = date("H:i:s", strtotime($attendanceLog->date));
            // if the in out mode is 'in', create a new transaction with '23:59'
            // as the end time
            if ($attendanceLog->in_out_mode == 0 || $attendanceLog->in_out_mode == 4) {
                $attendanceTransaction = AttendanceTransaction::create([
                    'date' => $date,
                    'staff_id' => $staff->id,
                    'checked_in_on' => $time,
                    'checked_out_on' => '23:59:00',
                    'work_type' => $attendanceLog->in_out_mode == 0 ? 'Normal' : 'Lembur',
                    'absent_type' => 'Hadir',
                    'is_excluded' => false
                ]);
            }
            // else if the in out type is 'out', then try search
            // for the last 'in' row first
            // if there is one, update the end time,
            // else, make a new row with '00:00' as its start time
            else {
                $attendanceTransaction = AttendanceTransaction::where([
                    ['date', '=',  $date],
                    ['staff_id', '=', $staff->id],
                    ['checked_out_on', '=', '23:59:00']
                ])->orderBy('date', 'DESC')->first();
                if ($attendanceLog->in_out_mode == 1 || $attendanceLog->in_out_mode == 5) {
                    if ($attendanceTransaction) {
                        $attendanceTransaction->update([
                            'checked_out_on' => $time
                        ]);
                    }
                    else {
                        $attendanceTransaction = AttendanceTransaction::create([
                            'date' => $date,
                            'staff_id' => $staff->id,
                            'checked_in_on' => '00:00:00',
                            'checked_out_on' => $time,
                            'work_type' => $attendanceLog->in_out_mode == 1 ? 'Normal' : 'Lembur',
                            'absent_type' => 'Hadir',
                            'is_excluded' => false
                        ]);
                    }
                }
            }
        }
        return response()->json($processedAttendanceLogs, 200);
    }
}

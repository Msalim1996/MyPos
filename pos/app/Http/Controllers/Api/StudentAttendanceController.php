<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudentAttendanceStatusType;
use App\Http\Common\Filter\FiltersSoftDelete;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentAttendanceResource;
use App\Http\Requests\StudentAttendanceStoreRequest as StoreRequest;
use App\Http\Requests\StudentAttendanceUpdateRequest as UpdateRequest;
use App\Models\ClassSchedule;
use App\Models\StudentAttendance;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class StudentAttendanceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * 
     */
    public function index()
    {
        $studentAttendances = QueryBuilder::for(StudentAttendance::class)
            ->allowedIncludes(['class_schedule.student_class.coach'])
            ->get();
        return StudentAttendanceResource::collection($studentAttendances);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $studentAttendance = StudentAttendance::create([
            'status' => $request->status,
            'remark' => $request->remark,
            'class_schedule_id' => $request->class_schedule_id,
            'member_id' => $request->member_id,
        ]);

        return new StudentAttendanceResource($studentAttendance->load(['classSchedule.studentClass.coach']));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $query = StudentAttendance::where('id', $id);
        $studentAttendance = QueryBuilder::for($query)
            ->allowedIncludes(['class_schedule.student_class.coach'])
            ->firstOrFail();
        return new StudentAttendanceResource($studentAttendance);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, StudentAttendance $studentAttendance)
    {
        $studentAttendance->update([
            'status' => $request->status,
            'remark' => $request->remark,
            'class_schedule_id' => $request->class_schedule_id,
            'member_id' => $request->member_id
        ]);

        if (StudentAttendanceStatusType::attended()->isEqual($studentAttendance->status)){
            $allStudentAttendances = ClassSchedule::where('id','=',$studentAttendance->class_schedule_id)->first()->studentAttendances;

            foreach($allStudentAttendances as $allStudentAttendance){
                if (!StudentAttendanceStatusType::attended()->isEqual($allStudentAttendance->status)){
                    $allStudentAttendance->status = StudentAttendanceStatusType::absent();
                    $allStudentAttendance->save();
                }
            }
        }

        return new StudentAttendanceResource($studentAttendance->load(['classSchedule.studentClass.coach']));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(StudentAttendance $studentAttendance)
    {
        $studentAttendance->delete();

        return response()->json(null, 204);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function unattendStudent($classScheduleId)
    {
        //get all related student attendance with the same class schedule id
        $studentAttendances = StudentAttendance::where('class_schedule_id','=',$classScheduleId)->get();
        //ex. if a class has 5 student, check if there is any student attendance that has 'Not Started' status
        //if there is any, then delete all student attendance with the same class schedules
        foreach($studentAttendances as $studentAttendance) $studentAttendance->delete();

        return response()->json(null, 204);
    }
}

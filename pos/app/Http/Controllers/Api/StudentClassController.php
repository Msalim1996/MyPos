<?php

namespace App\Http\Controllers\Api;

use App\Enums\StudentAttendanceStatusType;
use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\StudentClassStoreRequest as StoreRequest;
use App\Http\Requests\StudentClassUpdateRequest as UpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\StudentClassResource;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\StudentEnrollment;
use Carbon\Carbon;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Coach CRUD
 */
class StudentClassController extends Controller
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
        $studentClasses = QueryBuilder::for(StudentClass::class)
            ->allowedFilters([
                AllowedFilter::scope('enrollmentStatus'),
                'course_id'
            ])
            ->allowedIncludes(['level', 'coach', 'course', 'class_schedules', 'student_enrollments', 'student_enrollments.member'])
            ->get();
        return StudentClassResource::collection($studentClasses);
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
        $studentClass = StudentClass::create([
            'age_range' => $request->age_range,
            'date_start' => $request->date_start,
            'date_expired' => $request->date_expired,
            'remark' => $request->remark,
            'level_id' => $request->level_id,
            'coach_id' => $request->coach_id,
            'course_id' => $request->course_id,
            'cancelled_at' => $request->cancelled_at,
        ]);

        $classSchedules = [];
        if ($request->class_schedules) {
            for ($index = 0; $index < count($request->class_schedules); $index++) {
                $classSchedule = ClassSchedule::create([
                    'session_datetime' => $request->input('class_schedules.' . $index . '.session_datetime'),
                    'status' => 'Active', // When class created, default schedule status is active
                    'duration' => $request->input('class_schedules.' . $index . '.duration'),
                    'student_class_id' => $studentClass->id,
                ]);

                array_push($classSchedules, $classSchedule);
            }
        }

        if ($request->student_enrollments) {
            for ($index = 0; $index < count($request->student_enrollments); $index++) {
                $studentEnrollment = StudentEnrollment::create([
                    'enrollment_status' => $request->input('student_enrollments.' . $index . '.enrollment_status'),
                    'member_id' => $request->input('student_enrollments.' . $index . '.member_id'),
                    'student_class_id' => $studentClass->id,
                ]);

                //directly create student attendance
                foreach($classSchedules as $classSchedule)
                {
                    StudentAttendance::create([
                        'status' => StudentAttendanceStatusType::notStarted(),
                        'remark' => null,
                        'class_schedule_id' => $classSchedule->id,
                        'member_id' => $studentEnrollment->member_id
                    ]);
                }
            }
        }

        return new StudentClassResource($studentClass->load(['level', 'coach', 'course', 'classSchedules', 'studentEnrollments', 'studentEnrollments.member']));
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
        $query = StudentClass::where('id', $id);
        $studentClass = QueryBuilder::for($query)
            ->allowedIncludes(['level', 'coach', 'course', 'class_schedules', 'student_enrollments', 'student_enrollments.member'])
            ->firstOrFail();
        return new StudentClassResource($studentClass);
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
    public function update(UpdateRequest $request, StudentClass $studentClass)
    {
        $studentClass->update([
            'age_range' => $request->input('age_range'),
            'date_start' => $request->input('date_start'),
            'date_expired' => $request->input('date_expired'),
            'remark' => $request->input('remark'),
            'level_id' => $request->input('level_id'),
            'coach_id' => $request->input('coach_id'),
            'course_id' => $request->input('course_id'),
            'cancelled_at' => $request->input('cancelled_at'),
        ]);

        $classSchedules = [];
        if ($request->class_schedules) {
            for ($index = 0; $index < count($request->class_schedules); $index++) {
                $classSchedule = ClassSchedule::updateOrCreate([
                    'id' => $request->input('class_schedules.' . $index . '.id'),
                ], [
                    'session_datetime' => $request->input('class_schedules.' . $index . '.session_datetime'),
                    'duration' => $request->input('class_schedules.' . $index . '.duration'),
                    'status' => 'Active', // When class created, default schedule status is active
                    'student_class_id' => $studentClass->id,
                ]);

                array_push($classSchedules, $classSchedule->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempClassSchedules = $studentClass->classSchedules()->whereNotIn('id', $classSchedules)->get();
        foreach ($tempClassSchedules as $tempClassSchedule) {
            $tempStudentAttendances = $tempClassSchedule->studentAttendances()->get();
            foreach ($tempStudentAttendances as $tempStudentAttendance) $tempStudentAttendance->delete();
            $tempClassSchedule->delete();
        }
        $studentEnrollments = [];
        if ($request->student_enrollments) {
            for ($index = 0; $index < count($request->student_enrollments); $index++) {
                $studentEnrollment = StudentEnrollment::updateOrCreate([
                    'id' => $request->input('student_enrollments.' . $index . '.id'),
                ], [
                    'enrollment_status' => $request->input('student_enrollments.' . $index . '.enrollment_status'),
                    'member_id' => $request->input('student_enrollments.' . $index . '.member_id'),
                    'student_class_id' => $studentClass->id,
                ]);

                array_push($studentEnrollments, $studentEnrollment->id);
            }
        }
        // Remove one by one to make sure observer is called
        $tempStudentEnrollments = $studentClass->studentEnrollments()->whereNotIn('id', $studentEnrollments)->get();
        foreach ($tempStudentEnrollments as $tempStudentEnrollment) $tempStudentEnrollment->delete();

        return new StudentClassResource($studentClass->load(['level', 'coach', 'course', 'classSchedules', 'studentEnrollments', 'studentEnrollments.member']));
    }

    public function getClassesByMemberId(Request $request, $memberId)
    {
        $studentClasses = StudentClass::whereHas('studentEnrollments.member', function ($query) use ($memberId) {
            $query->where('member_id', '=', $memberId);
        })
            ->get();
        return StudentClassResource::collection($studentClasses);
    }

    public function cancel(UpdateRequest $request, $studentClassId)
    {
        $studentClass = StudentClass::where('id', '=', $studentClassId)->firstOrFail();
        $studentClass->update([
            'cancelled_at' => Carbon::now(),
        ]);

        $classSchedules = $studentClass->classSchedules()->get();
        foreach ($classSchedules as $classSchedule) {
            $classSchedule->status = 'Cancelled';
            $classSchedule->save();
            $studentAttendances = $classSchedule->studentAttendances()->get();
            foreach ($studentAttendances as $studentAttendance) {
                $studentAttendance->status = 'Cancelled';
                $studentAttendance->save();
            }
        }

        return response()->json(null, 204);
    }
}

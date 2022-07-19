<?php

namespace App\Http\Controllers\Api;

use App\Http\Common\Filter\FiltersSoftDelete;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

// VALIDATION: change the requests to match your own file names if you need form validation
use App\Http\Requests\StudentClassStoreRequest as StoreRequest;
use App\Http\Requests\StudentClassUpdateRequest as UpdateRequest;
use App\Http\Resources\CourseResource;
use App\Http\Resources\StudentClassResource;
use App\Http\Resources\StudentEnrollmentResource;
use App\Models\ClassSchedule;
use App\Models\Course;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use App\Models\StudentEnrollment;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group Coach CRUD
 */
class StudentEnrollmentController extends Controller
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
        $studentEnrollments = QueryBuilder::for(StudentEnrollment::class)
            ->allowedIncludes(['member', 'student_class'])
            ->get();
        return StudentEnrollmentResource::collection($studentEnrollments);
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
        $studentEnrollment = StudentEnrollment::create([
            'enrollment_status' => $request->enrollment_status,
            'member_id' => $request->member_id,
            'student_class_id' => $request->student_class_id
        ]);

        // create attendance base on the member and the number of lessons
        $member = $studentEnrollment->member;
        $classSchedules = $studentEnrollment->studenClass->classSchedules;
        for ($index = 0; $index < count($classSchedules); $index++) {
            StudentAttendance::create([
                'status' => "Not started",
                'class_schedule_id' => $classSchedules[$index]->id,
                'member_id' => $member->id,
            ]);
        }

        return new StudentEnrollmentResource($studentEnrollment->load(['member', 'studentClass']));
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
        $query = StudentEnrollment::where('member_id', $id);
        $studentEnrollment = QueryBuilder::for($query)
            ->allowedIncludes(['member', 'student_class'])
            ->firstOrFail();
        return new StudentEnrollmentResource($studentEnrollment);
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
    public function update(UpdateRequest $request, StudentEnrollment $studentEnrollment)
    {
        $studentEnrollment->update([
            'enrollment_status' => $request->input('enrollment_status'),
            'member_id' => $request->input('member_id'),
            'student_class_id' => $request->input('student_class_id')
        ]);

        // update student attendance
        $studentAttendances = [];
        if ($request->student_attendances) {
            for ($index = 0; $index < count($request->student_attendances); $index++) {
                $studentAttendance = StudentAttendance::updateOrCreate([
                    'id' => $request->input('student_attendances.' . $index . '.id'),
                ], [
                    'status' => $request->input('student_attendances.' . $index . '.status'),
                    'remark' => $request->input('student_attendances.' . $index . '.remark'),
                    'class_schedule_id' => $request->input('student_class_id'),
                    'member_id' => $request->input('member_id'),
                ]);

                array_push($studentAttendances, $studentAttendance->id);
            }
        }

        // Remove one by one to make sure observer is called
        $member = $studentEnrollment->member;
        $tempStudentAttendances = $member->studentAttendances()->whereNotIn('id', $studentAttendances)->get();
        foreach ($tempStudentAttendances as $tempStudentAttendance) $tempStudentAttendance->delete();

        return new StudentEnrollmentResource($studentEnrollment->load(['member', 'studentClass']));
    }

    public function searchStudentEnrollment($id)
    {
        // check if the id given is class_id, if not found, proceed to search by ticket 
        $studentEnrollment = StudentEnrollment::with(['studentClass'])->whereHas('studentClass', function ($query) use ($id) {
            return $query->where([
                ['class_id', '=', $id]
                ]);
        })->get();
        if (count($studentEnrollment) != 0) {
            return StudentEnrollmentResource::collection($studentEnrollment);
        }

        // check if the id given is member_id, if not found, return 404
        $studentEnrollment = StudentEnrollment::with(['member'])->whereHas('member', function ($query) use ($id) {
            return $query->where([
                ['member_id', '=', $id]
                ]);
        })->get();
        if (count($studentEnrollment) != 0) {
            return studentEnrollmentResource::collection($studentEnrollment);
        }

        return response()->json(['message' => 'Data tidak ditemukan'], 404);
    }
}

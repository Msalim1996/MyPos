<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\CoachCommissionRequest as QueryRequest;

class CoachScheduleJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $classSchedules = DB::select(DB::raw(
            "
            SELECT 
                coaches.coach_id AS coach_id,
                coaches.name AS coach,
                members.member_id AS member_id,
                members.name AS member,
                courses.name AS lesson,
                class_schedules.session_datetime AS lesson_time,
                student_classes.age_range,
                class_schedules.duration
            FROM student_classes
            LEFT JOIN class_schedules ON student_classes.id = class_schedules.student_class_id
            LEFT JOIN student_enrollments ON student_classes.id = student_enrollments.student_class_id
            LEFT JOIN members ON student_enrollments.member_id = members.id
            LEFT JOIN courses ON  student_classes.course_id = courses.id
            RIGHT JOIN coaches ON student_classes.coach_id = coaches.id
            WHERE
                class_schedules.session_datetime >= :class_schedules_start_date
                AND class_schedules.session_datetime <= :class_schedules_end_date
            ORDER BY class_schedules.session_datetime ASC
            "
        ), [
            "class_schedules_start_date" => $request->filter['start_date'],
            "class_schedules_end_date" => $request->filter['end_date'] . ' 23:59:59',
        ]);

        return response()->json([
            'data' => $data,
            'class_schedules' => $classSchedules,
        ], 200);
    }
}

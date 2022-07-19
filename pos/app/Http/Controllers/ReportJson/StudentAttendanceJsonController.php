<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\StudentAttendanceRequest as QueryRequest;

class StudentAttendanceJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
            "coach" => $request->filter['coach'],
        ];

        $classSchedules = DB::select(DB::raw(
            "
            SELECT 
                student_attendances.id as student_attendance_id,
                student_attendances.status as student_attendance_status,
                student_attendances.remark as student_attendance_remark,
                DATE(class_schedules.session_datetime) as lesson_date,
                TIME(class_schedules.session_datetime) as lesson_time,
                class_schedules.duration,
                members.member_id as member_id,
                members.name as member,
                student_classes.age_range,
                student_classes.class_id,
                courses.name as lesson,
                coaches.coach_id as coach_id,
                coaches.name as coach,
                levels.name as level_name
            FROM student_attendances
            LEFT JOIN class_schedules ON student_attendances.class_schedule_id = class_schedules.id
            LEFT JOIN members ON student_attendances.member_id = members.id
            LEFT JOIN student_classes ON student_classes.id = class_schedules.student_class_id
            LEFT JOIN courses ON  student_classes.course_id = courses.id
            LEFT JOIN coaches ON student_classes.coach_id = coaches.id
            LEFT JOIN levels ON student_classes.level_id = levels.id
            WHERE
                class_schedules.session_datetime >= :class_schedules_start_date
                AND class_schedules.session_datetime <= :class_schedules_end_date
                AND coaches.coach_id LIKE :coach
            ORDER BY class_schedules.session_datetime ASC
            "
        ), [
            "class_schedules_start_date" => $request->filter['start_date'],
            "class_schedules_end_date" => $request->filter['end_date'] . ' 23:59:59',
            "coach" => $request->filter['coach']
        ]);

        return response()->json([
            'data' => $data,
            'class_schedules' => $classSchedules,
        ], 200);
    }
}

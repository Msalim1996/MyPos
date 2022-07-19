<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\CoachCommissionRequest as QueryRequest;
use App\Models\CoachCommission;

class CoachCommissionJsonController extends Controller
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
                class_schedules.id,
                student_classes.class_id as student_class_id,
                coaches.id as coach_id,
                coaches.coach_id as coach_code,
                coaches.name as coach_name,
                courses.name as course_name,
                courses.price as course_price,
                courses.day_type,
                courses.course_type,
                courses.coach_type,
                level_groups.name as level_group_name,
                student_enrollments.id as student_enrollment_id,
                student_enrollments.member_id as student_enrollment_member_id,
                student_attendances.id as student_att_id,
                student_attendances.status,
                student_attendances.member_id,
                COUNT(DISTINCT(student_enrollments.id)) as total_student,
                (COUNT(student_enrollments.id) / COUNT(DISTINCT(student_enrollments.id))) as total_lesson
            FROM class_schedules
            LEFT JOIN student_classes ON student_classes.id = class_schedules.student_class_id
            LEFT JOIN coaches ON coaches.id = student_classes.coach_id
            LEFT JOIN courses ON courses.id = student_classes.course_id
            LEFT JOIN level_groups ON level_groups.id = courses.level_group_id
            LEFT JOIN student_attendances ON student_attendances.class_schedule_id = class_schedules.id
            LEFT JOIN student_enrollments ON student_enrollments.student_class_id = student_classes.id AND student_enrollments.member_id = student_attendances.member_id
            WHERE
                (student_attendances.status = 'Attended' OR student_attendances.status = 'Absent') AND
                class_schedules.session_datetime >= :class_schedules_start_date AND
                class_schedules.session_datetime <= :class_schedules_end_date
            GROUP BY student_class_id
            "
        ), [
            "class_schedules_start_date" => $request->filter['start_date'],
            "class_schedules_end_date" => $request->filter['end_date'] . ' 23:59:59',
        ]);

        // Count coach lesson grand total
        $coachCommissionPercentageList = array();
        foreach ($classSchedules as $classSchedule) {
            $coachId = $classSchedule->coach_id;
            $coachLessonTotal = $classSchedule->total_lesson;
            if (array_key_exists($coachId, $coachCommissionPercentageList) == false) {
                $coachCommissionPercentageList[$coachId] = array(
                    'coach_id' => $coachId,
                    'coach_lesson_total' => $coachLessonTotal
                );
            } else {
                $coachCommissionPercentageList[$coachId]['coach_lesson_total'] += $coachLessonTotal;
            }
        }

        $classScheduleWithDetail = [];
        // Count coach commission percentage by lesson grand total
        $coachCommissions = array();
        foreach ($coachCommissionPercentageList as $coachCommission) {
            $commission = 0;
            // refer commission table
            $coachCommissionDatas = CoachCommission::where('coach_id','=',$coachCommission['coach_id'])->orderBy('commission_class', 'asc')->get();
            foreach($coachCommissionDatas as $coachCommissionData){
                if ($coachCommission['coach_lesson_total'] <= $coachCommissionData->commission_class) {
                    $commission = $coachCommissionData->commission_percentage;
                    break;
                }
            }

            foreach ($classSchedules as $classSchedule) {
                if ($classSchedule->coach_id == $coachCommission['coach_id']) {
                    $newColumns = array(
                        'coach_lesson_total' => $coachCommission['coach_lesson_total'],
                        'commission' => (string) $commission
                    );
                    array_push($classScheduleWithDetail, array_merge((array) $classSchedule, $newColumns));
                }
            }
        }

        return response()->json([
            'data' => $data,
            'class_schedules' => $classScheduleWithDetail
        ], 200);
    }
}

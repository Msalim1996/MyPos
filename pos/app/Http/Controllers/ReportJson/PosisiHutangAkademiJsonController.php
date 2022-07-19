<?php

namespace App\Http\Controllers\ReportJson;

use App\Enums\SalesItemType;
use App\Enums\StudentAttendanceStatusType;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Reports\PosisiHutangAkademiQueryRequest as QueryRequest;
use App\Models\SalesItem;
use App\Models\StudentAttendance;
use App\Models\StudentClass;
use stdClass;

class PosisiHutangAkademiJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "end_date" => $request->filter['end_date'],
        ];

        $posisiHutang = [];
        $studentClasses = StudentClass::where('date_start', '<=', $request->filter['end_date'])->get();
        foreach ($studentClasses as $studentClass) {
            if (!$studentClass->cancelled_at) {
                $course = $studentClass->course;
                $studentEnrollments = $studentClass->studentEnrollments;
                foreach ($studentEnrollments as $studentEnrollment) {
                    if ($studentEnrollment->enrollment_status == 'Paid') {
                        $member = $studentEnrollment->member;
                        $hutang = new stdClass;
                        $hutang->member_id = $member->member_id;
                        $hutang->member_name = $member->name;
                        $hutang->class_id = $studentClass->class_id;
                        $classSchedules = $studentClass->classSchedules;
                        $notStartedCount = 0;
                        $studentAttendanceCount = 0;
                        $invalidStudentAttendanceCount = 0;
                        foreach ($classSchedules as $classSchedule) {
                            $studentAttendance = StudentAttendance::where([
                                ['class_schedule_id', $classSchedule->id],
                                ['member_id', $member->id]
                            ])->first();
                            if ($studentAttendance) {
                                $studentAttendanceCount++;
                                if ($classSchedule->session_datetime <= $request->filter['end_date'] && $studentAttendance->status != StudentAttendanceStatusType::notStarted()) {
                                    $invalidStudentAttendanceCount++;
                                }
                            }

                            $studentAttendances = StudentAttendance::where('member_id', $member->id)->where('class_schedule_id', $classSchedule->id);
                            if ($classSchedule->session_datetime <= $request->filter['end_date']) {
                                $studentAttendances = $studentAttendances->where('status', StudentAttendanceStatusType::notStarted())->get();
                                $notStartedCount += count($studentAttendances);
                            }
                            else if ($classSchedule->session_datetime > $request->filter['end_date']) {
                                $studentAttendances = $studentAttendances->get();
                                $notStartedCount += count($studentAttendances);
                            }
                        }
                        if ($studentAttendanceCount < $course->number_of_lessons) {
                            $notStartedCount = $course->number_of_lessons - $invalidStudentAttendanceCount;
                        }
                        $salesItem = SalesItem::where([
                            ['item_id', $studentEnrollment->id],
                            ['item_type', SalesItemType::studentEnrollment()]
                        ])->first();
                        $hutang->not_started_count = $notStartedCount;
                        $hutang->number_of_lessons = $course->number_of_lessons;
                        $hutang->course_start_date = $studentClass->date_start;
                        $hutang->course_expired_date = $studentClass->date_expired;
                        $hutang->course_price = $salesItem->getSubTotal();

                        array_push($posisiHutang, $hutang);
                    }
                }
            }
        }

        return response()->json([
            'data' => $data,
            'posisi_hutang' => $posisiHutang,
        ], 200);
    }
}

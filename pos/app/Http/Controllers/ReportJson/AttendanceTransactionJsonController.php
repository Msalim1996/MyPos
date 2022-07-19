<?php

namespace App\Http\Controllers\ReportJson;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Reports\AttendanceTransactionRequest as QueryRequest;
use App\Models\Staff;
use DateTime;

class AttendanceTransactionJsonController extends Controller
{
    public function index(QueryRequest $request)
    {
        $data = [
            "staff_id" => $request->filter['staff_id'],
            "start_date" => $request->filter['start_date'],
            "end_date" => $request->filter['end_date'],
        ];

        $paySummaryArray = [];
        $paySummary = DB::select(DB::raw(
            "
            SELECT 
                staffs.name,
                staffs.position,
                0.00 as time_diff,
                attendance_transactions.date,
                attendance_transactions.checked_in_on,
                attendance_transactions.checked_out_on,
                attendance_transactions.work_type,
                attendance_transactions.absent_type,
                attendance_transactions.is_excluded,
                attendance_transactions.description,
                attendance_transactions.pic,
                attendance_transactions.verified_by
            FROM staffs
            LEFT JOIN attendance_transactions ON staffs.id = attendance_transactions.staff_id
            WHERE 
                staffs.id = :staff_id AND
                attendance_transactions.date >= :attendance_start_date AND
                attendance_transactions.date <= :attendance_end_date
            "
        ), [
            "attendance_start_date" => $request->filter['start_date'],
            "attendance_end_date" => $request->filter['end_date'],
            "staff_id" => $request->filter['staff_id']
        ]);

        $distinctDate = DB::select(DB::raw(
            "
            SELECT 
                DISTINCT attendance_transactions.date
            FROM staffs
            LEFT JOIN attendance_transactions ON staffs.id = attendance_transactions.staff_id
            WHERE 
                staffs.id = :staff_id AND
                attendance_transactions.date >= :attendance_start_date AND
                attendance_transactions.date <= :attendance_end_date
            "
        ), [
            "attendance_start_date" => $request->filter['start_date'],
            "attendance_end_date" => $request->filter['end_date'],
            "staff_id" => $request->filter['staff_id']
        ]);
        $distinctDateArray = [];
        foreach($distinctDate as $date)
            array_push($distinctDateArray, $date->date);

        $listOfDate = DB::select(DB::raw(
            "
            SELECT * from 
                (SELECT adddate('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) date from
                (SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t0,
                (SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t1,
                (SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t2,
                (SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t3,
                (SELECT 0 i union SELECT 1 union SELECT 2 union SELECT 3 union SELECT 4 union SELECT 5 union SELECT 6 union SELECT 7 union SELECT 8 union SELECT 9) t4) v
                WHERE date BETWEEN :attendance_start_date AND :attendance_end_date
            "
        ), [
            "attendance_start_date" => $request->filter['start_date'],
            "attendance_end_date" => $request->filter['end_date'],
        ]);
        $listOfDateArray = [];
        foreach($listOfDate as $date)
            array_push($listOfDateArray, $date->date);

        $dummyDate = array_diff($listOfDateArray, $distinctDateArray);

        $totalOvertimeHours = 0;
        $totalNormalHours = 0;
        foreach ($paySummary as $payment) {
            $checkedInOn = new DateTime($payment->date . ' ' . $payment->checked_in_on);
            $checkedOutOn = new DateTime($payment->date . ' ' . $payment->checked_out_on);
            $timeDiff = $checkedInOn->diff($checkedOutOn);
            $hours = $timeDiff->h;
            $minutes = $timeDiff->i;
            $timeAddition = number_format($hours + ($minutes / 60), 2);
            $payment->time_diff = $timeAddition;
            if (!$payment->is_excluded) {
                if ($payment->work_type == "Normal") 
                    $totalNormalHours += $timeAddition;
                else if ($payment->work_type == "Lembur")
                    $totalOvertimeHours += $timeAddition;
            }
            array_push($paySummaryArray, $payment);
        }

        $staff = Staff::where('id', $request->filter['staff_id'])->first();
        foreach($dummyDate as $date) {
            array_push($paySummaryArray, (object) [
                'name' => $staff->name,
                'position' => $staff->position,
                'time_diff' => null,
                'date' => $date,
                'checked_in_on' => null,
                'checked_out_on' => null,
                'work_type' => null,
                'absent_type' => null,
                'is_excluded' => null,
                'description' => null,
                'pic' => null,
                'verified_by' => null,
            ]);
        }

        $totalWorkingHours = (float)number_format($totalOvertimeHours + $totalNormalHours, 2);
        $totalHours = [
            'total_overtime_hours' => $totalOvertimeHours,
            'total_normal_hours' => $totalNormalHours,
            'total_working_hours' => $totalWorkingHours
        ];

        return response()->json([
            'data' => $data,
            'pay_summary' => $paySummaryArray,
            'total_hours' => $totalHours
        ], 200);
    }
}

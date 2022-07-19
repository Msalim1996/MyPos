<?php

namespace App\Http\Controllers\Api;

use App\Events\GateTicket\OnGoingTicketInDeleteEvent;
use App\Events\GateTicket\OnGoingTicketOutCreateEvent;
use App\Events\GateTicket\OnGoingTicketOutDeleteEvent;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use App\Models\Barcode;
use Carbon\Carbon;
use App\Models\GSTimeSchedule;
use App\Models\BarcodeType;
use App\User;
use App\models\GateRinkTransaction;

/**
 * Gate rink access control and transaction CRUD
 *
 * @group Gate (Rink) access control & transaction CRUD
 */
class J8GateRinkController extends Controller
{
    /**
     * Check in ticket immediately, else return error message and the result
     *
     * @bodyParam barcode_id int required
     * @return \Illuminate\Http\Response
     */
    public function checkIn(Request $request)
    {
        // Get barcode type by the given barcode id
        // TODO duplicated code found in checkout functionality, refactor code
        $barcodeSplitArr = preg_split('/(?<=[\sa-zA-Z])(?=[0-9])/', $request->barcode_id);
        if (count($barcodeSplitArr) != 2 && preg_match('/[^A-Za-z]/', $barcodeSplitArr[0])) {
            return response()->json(['message' => 'Format barcode salah'], 404);
        }
        $prefix = $barcodeSplitArr[0];
        $barcodeType = BarcodeType::findOrFail($prefix);

        switch ($barcodeType->type) {
            case 'PUBLIC':
            case 'STUDENT':
            case 'COMPLIMENT':
                return $this->checkInPlayer($request, $request->barcode_id);
                break;
            case 'STAFF':
                return $this->checkInStaff($request, $request->barcode_id);
                break;
            default:
                return response()->json(['message' => 'Barcode tidak diperbolehkan masuk'], 404);
                break;
        }
    }

    /**
     * Check out ticket immediately, else return error message and the result
     *
     * @bodyParam barcode_id int required
     * @return \Illuminate\Http\Response
     */
    public function checkOut(Request $request)
    {
        // Get barcode type by the given barcode id
        $barcodeSplitArr = preg_split('/(?<=[\sa-zA-Z])(?=[0-9])/', $request->barcode_id);
        if (count($barcodeSplitArr) != 2 && preg_match('/[^A-Za-z]/', $barcodeSplitArr[0])) {
            return response()->json(['message' => 'Format barcode salah'], 404);
        }
        $prefix = $barcodeSplitArr[0];
        $barcodeType = BarcodeType::findOrFail($prefix);

        switch ($barcodeType->type) {
            case 'PUBLIC':
            case 'STUDENT':
            case 'COMPLIMENT':
                return $this->checkOutPlayer($request, $request->barcode_id);
                break;
            case 'STAFF':
                return $this->checkOutStaff($request, $request->barcode_id);
                break;
            default:
                return response()->json(['message' => 'Barcode tidak diperbolehkan keluar'], 404);
                break;
        }
    }

    private function checkInPlayer(Request $request, $barcodeId)
    {
        $errorList = [];
        // check if barcode activated
        if (!Barcode::where('barcode_id', '=', $barcodeId)->exists()) {
            array_push($errorList, 'Barcode belum diaktivasi');
        } else {
            $gsGateType = GeneralSetting::all()->first()->gate_control_type;
            $barcode = Barcode::where('barcode_id', '=', $barcodeId)->get()->first();
            switch ($gsGateType) {
                case "whole day":
                    $gateRinkTransaction = GateRinkTransaction::create(['barcode_id' => $barcodeId, 'time_in' => Carbon::now()]);

                    // trigger on going ticket
                    event(new OnGoingTicketInDeleteEvent(array($barcode)));
                    event(new OnGoingTicketOutCreateEvent(array($barcode)));
                    break;
                case "time interval":
                    // check if datetime is still in the session period
                    $barcodeCreationDateTime = Barcode::where('barcode_id', '=', $barcodeId)->get()->first()->active_on;
                    if ($this->isTimePeriodValid($barcodeCreationDateTime, $errorList)) {
                        $gateRinkTransaction = GateRinkTransaction::create(['barcode_id' => $barcodeId, 'time_in' => Carbon::now()]);

                        // trigger on going ticket
                        event(new OnGoingTicketInDeleteEvent(array($barcode)));
                        event(new OnGoingTicketOutCreateEvent(array($barcode)));
                    }
                    break;
                default:
                    return response(array('result' => false, 'message' => 'Gate control type salah, silahkan hubungi admin'));
                    break;
            }
        }

        return response(array('result' => empty($errorList), 'message' => $errorList));
    }

    private function checkOutPlayer(Request $request, $barcodeId)
    {
        $errorList = [];
        // check if barcode activated
        if (!Barcode::where('barcode_id', '=', $barcodeId)->exists()) {
            array_push($errorList, 'Barcode belum diaktivasi');
        } else {
            $gsGateType = GeneralSetting::all()->first()->gate_control_type;
            $barcode = Barcode::where('barcode_id', '=', $barcodeId)->get()->first();
            switch ($gsGateType) {
                case "whole day":
                    if (GateRinkTransaction::where('barcode_id', '=', $barcodeId)->exists()) {
                        $gateRinkTransaction = GateRinkTransaction::where('barcode_id', '=', $barcodeId)
                            ->orderBy('created_at', 'desc')->get()->first();
                        if (is_null($gateRinkTransaction->time_out)) {
                            $gateRinkTransaction->time_out = Carbon::now();
                            $gateRinkTransaction->save();

                            // trigger on going ticket
                            event(new OnGoingTicketOutDeleteEvent(array($barcode)));
                        } else {
                            // user went out, should not allow go out second time
                            array_push($errorList, 'User sudah keluar, tidak diperbolehkan scan 2 kali');
                        }
                    } else {
                        array_push($errorList, 'User harus masuk terlebih dahulu sebelum keluar');
                    }
                    break;
                case "time interval":
                    // get the last gate transaction "time_out" list
                    $gateRinkTransactions = GateRinkTransaction::where('barcode_id', '=', $barcodeId)
                        ->whereNull('time_out')
                        ->orderBy('created_at', 'desc')
                        ->get();

                    if ($gateRinkTransactions->isEmpty()) {
                        // user never come in
                        array_push($errorList, 'User harus masuk terlebih dahulu sebelum keluar');
                    } else {
                        // if user enter multiple time by accident, set all multiple "time_out" into current time
                        foreach ($gateRinkTransactions as $gateRinkTransaction) {
                            $gateRinkTransaction->time_out = Carbon::now();
                            $gateRinkTransaction->save();
                        }

                        // trigger on going ticket
                        event(new OnGoingTicketOutDeleteEvent(array($barcode)));
                    }
                    break;
                default:
                    return response(array('result' => false, 'message' => 'Gate control type salah, silahkan hubungi admin'));
                    break;
            }
        }

        return response(array('result' => empty($errorList), 'message' => $errorList));
    }

    private function checkInStaff(Request $request, $barcodeId)
    {
        $errorList = [];
        // check if staff register
        if (!User::where('username', '=', $barcodeId)->exists()) {
            array_push($errorList, 'Id staff tidak ditemukan');
            return response(array('result' => empty($errorList), 'message' => $errorList));
        }

        // check if staff status is active
        $staff = User::where('username', '=', $barcodeId)->get()->first();
        if ($staff->trashed()) {
            array_push($errorList, 'status staff tidak aktif');
            return response(array('result' => empty($errorList), 'message' => $errorList));
        }

        GateRinkTransaction::create(['barcode_id' => $barcodeId, 'time_in' => Carbon::now()]);
        return response(array('result' => empty($errorList), 'message' => $errorList));
    }

    private function checkOutStaff(Request $request, $barcodeId)
    {
        $errorList = [];
        // check if staff register
        if (!User::where('username', '=', $barcodeId)->exists()) {
            array_push($errorList, 'Id staff tidak ditemukan');
            return response(array('result' => empty($errorList), 'message' => $errorList));
        }

        // check if staff status is active
        $staff = User::where('username', '=', $barcodeId)->get()->first();
        if ($staff->trashed()) {
            array_push($errorList, 'status staff tidak aktif');
            return response(array('result' => empty($errorList), 'message' => $errorList));
        }

        // Staff has a privelege where staff can go out multiple time
        // If there exists null time out, input it there
        // If not, take the last gate transaction and update the time
        $gateRinkTransactions = GateRinkTransaction::where('barcode_id', '=', $barcodeId)
            ->whereNull('time_out')
            ->orderBy('created_at', 'desc')
            ->get();

        if ($gateRinkTransactions->isEmpty()) {
            // if staff check out several time, the second time onwards will only update the last gate transaction
            $gateRinkTransaction = GateRinkTransaction::where('barcode_id', '=', $barcodeId)
                ->orderBy('created_at', 'desc')
                ->get()->first();
            $gateRinkTransaction->time_out = Carbon::now();
            $gateRinkTransaction->save();
        } else {
            // if staff enter multiple time, set all multiple "time_out" into current time
            foreach ($gateRinkTransactions as $gateRinkTransaction) {
                $gateRinkTransaction->time_out = Carbon::now();
                $gateRinkTransaction->save();
            }
        }
        return response(array('result' => empty($errorList), 'message' => $errorList));
    }

    private function isTimePeriodValid($barcodeCreationDateTime, &$errorList)
    {
        // if user check in different date, reject user
        if (!$barcodeCreationDateTime->isToday()) {
            array_push($errorList, 'Masa aktif barcode berakhir');
            return false;
        }

        // check if the user in his/her time interval
        $barcodeTime = new Carbon($barcodeCreationDateTime->toTimeString());
        $currentTime = Carbon::now();
        $barcodeTimeSession = $this->getTimePeriod($barcodeTime);
        $currentTimeSession = $this->getTimePeriod($currentTime);
        // need to also check if the time is not in one of the period
        if ($barcodeTimeSession == $currentTimeSession && ($barcodeTimeSession != "" || $currentTimeSession != "")) {
            return true;
        } else {
            if ($currentTime->lessThan($barcodeTime)) {
                array_push($errorList, 'Belum diperbolehkan masuk');
            } else {
                array_push($errorList, 'Masa aktif barcode berakhir');
            }
            return false;
        }
    }

    private function getTimePeriod($time)
    {
        $timePeriods = GSTimeSchedule::where('day', '=', $time->englishDayOfWeek)->get();
        foreach ($timePeriods as $timePeriod) {
            if ($time->between(new Carbon($timePeriod->start_time), new Carbon($timePeriod->end_time))) {
                return $timePeriod->day . $timePeriod->name;
            }
        }
        // if not in any time period, return empty string
        return '';
    }
}

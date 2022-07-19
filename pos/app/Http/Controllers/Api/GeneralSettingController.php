<?php

namespace App\Http\Controllers\Api;

use App\Enums\GateModeType;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\GeneralSettingResource;
use App\Models\GeneralSetting;

use App\Http\Requests\GeneralSettingRequest as UpdateRequest;
use App\Http\Resources\GSTimeScheduleResource;
use App\Models\GSTimeSchedule;
use App\Models\Item;
use Carbon\Carbon;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @group General setting functionality
 */
class GeneralSettingController extends Controller
{
    /**
     * Display first resource of general setting.
     * 
     * By right, general setting should only contain 1 setting
     * 
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $generalSetting = QueryBuilder::for(GeneralSetting::class)
            ->firstOrFail();
        return new GeneralSettingResource($generalSetting);
    }


    /**
     * Update the specified resource in storage.
     * bodyParam:
     * {
     *   gate_type: "time interval",
     *   time_schedules: [
     *     {
     *       "id": 1,
     *       "name": "Sesi 1",
     *       "day": "Monday",
     *       "start_time": "10:00:00",
     *       "end_time": "12:30:00"
     *     }
     *   ]
     * } 
     * @authenticated
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request)
    {
        // update general setting
        $generalSetting = GeneralSetting::firstOrFail();
        $generalSetting->update($request->only([
            'gate_control_type',
            'company_name',
            'company_email',
            'company_phone',
            'company_address',
            'skating_aid_timeout',
            'tax_payer',
            'tax_number',
            'affirmation_date',
            'logo',
            'tax_toggle',
            'tax_amount',
            'gate_mode'
        ]));

        //if the tax is changed, change all items with tax to the inputted general setting's tax
        if ($generalSetting->tax_amount > 0 && $generalSetting->tax_toggle){
            $items = Item::get();
            foreach ($items as $item){
                if (($item->tax > 0)) $item->tax = $generalSetting->tax_amount;
            }
        }

        // if image is provided, also upload image
        if ($request->image && $request->input('image') != "") {
            $generalSetting->addMediaFromBase64($request->image, ['image/jpeg', 'image/png', 'image/jpg', 'image/gif'])
                ->usingFileName(Carbon::now()->format('Y-m-d_H-i') . '.tmp')
                ->toMediaCollection(GeneralSetting::$mediaCollectionPath);
        }

        // update gs time schedules
        $timeSchedules = [];
        if ($request->time_schedules) {
            for ($index = 0; $index < count($request->time_schedules); $index++) {
                $timeSchedule = GSTimeSchedule::updateOrCreate([
                    'id' => $request->input('time_schedules.' . $index . '.id'),
                ], [
                    'name' => $request->input('time_schedules.' . $index . '.name'),
                    'day' => $request->input('time_schedules.' . $index . '.day'),
                    'start_time' => $request->input('time_schedules.' . $index . '.start_time'),
                    'end_time' => $request->input('time_schedules.' . $index . '.end_time'),
                ]);

                array_push($timeSchedules, $timeSchedule->id);
            }
        }
        // Remove one by one to make sure observer is called if any
        $tempTimeSchedules = GSTimeSchedule::whereNotIn('id', $timeSchedules)->get();
        foreach ($tempTimeSchedules as $tempTimeSchedule) $tempTimeSchedule->delete();
        
        return new GeneralSettingResource(GeneralSetting::firstOrFail());
    }

    /**
     * Display list of available gate type 
     * 
     * @authenticated
     *
     * @return \Illuminate\Http\Response
     */
    public function gateControlTypeList()
    {
        return response(['whole day', 'time interval'], 200);
    }

    /**
     * GET item types
     * 
     * @authenticated
     * 
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function gateModeTypes(Request $request)
    {
        return response()->json(['data' => GateModeType::getValues()], 200);
    }
}

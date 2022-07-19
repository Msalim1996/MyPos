<?php

namespace App\Http\Resources;

use App\Models\GSTimeSchedule;
use Illuminate\Http\Resources\Json\JsonResource;

class GeneralSettingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'tax_payer' => $this->tax_payer,
            'tax_number' => $this->tax_number,
            'affirmation_date' => $this->affirmation_date,
            'logo' => $this->logo,
            'tax_toggle' => $this->tax_toggle,
            'tax_amount' => $this->tax_amount,
            'company_name' => $this->company_name,
            'company_email' => $this->company_email,
            'company_phone' => $this->company_phone,
            'company_address' => $this->company_address,
            'gate_control_type' => $this->gate_control_type,
            'skating_aid_timeout' => $this->skating_aid_timeout,
            'gate_mode' => $this->gate_mode,
            /**
             * FIXME: This code is still not the best way to query time_schedules
             * 
             * Since we are using query builder, the cleanest way is to include time_schedules into include 
             * parameter, but general setting and time schedule does not have any relation in db whatsoever
             * So, the current way is to include time schedule parameter with value true to get query
             * 
             * e.g. http://localhost:8000/api/general-setting?time_schedules=true
             */
            'time_schedules' => $this->when($request->input('time_schedules') == true, function () {
                return GSTimeScheduleResource::collection(GSTimeSchedule::all());
            }),
            'available_gate_control_types' => ['whole day', 'time interval']
        ];
    }
}

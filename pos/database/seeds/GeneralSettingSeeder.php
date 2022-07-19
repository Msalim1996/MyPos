<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GeneralSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('general_settings')->insert([
            'id' => 1,
            // 'gate_type' => 'whole day',
            'gate_control_type' => 'time interval',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'tax_payer' => null,
            'tax_number' => null,
            'affirmation_date' => Carbon::now(),
            'logo' => null,
            'tax_toggle' => false,
            'tax_amount' => 0
        ]);

        DB::table('locations')->insert([
            'id' => 1,
            'name' => 'Default location',
            'default_location' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

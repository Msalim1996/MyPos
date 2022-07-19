<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ShortcutTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**
         * Default shortcut day types
         */

        DB::table('shortcut_day_types')->insert([
            'id' => 1,
            'name' => 'Weekend',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('shortcut_day_types')->insert([
            'id' => 2,
            'name' => 'Weekday',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('shortcut_day_types')->insert([
            'id' => 3,
            'name' => 'Holiday',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * Default shortcut day
         */

        DB::table('shortcut_days')->insert([
            'id' => 1,
            'on_date' => '2019-12-25',
            'description' => 'Christmas',
            'shortcut_day_type_id' => 3,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}


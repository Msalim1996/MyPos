<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class GSTimeScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Monday
         */

        DB::table('g_s_time_schedules')->insert([
            'id' => 1,
            'name' => 'Sesi 1',
            'day' => 'Monday',
            'start_time' => '10:00',
            'end_time' => '12:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 2,
            'name' => 'Sesi 2',
            'day' => 'Monday',
            'start_time' => '13:00',
            'end_time' => '15:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 3,
            'name' => 'Sesi 3',
            'day' => 'Monday',
            'start_time' => '16:00',
            'end_time' => '18:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 4,
            'name' => 'Sesi 4',
            'day' => 'Monday',
            'start_time' => '19:00',
            'end_time' => '21:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        
        /**
         * Tuesday
         */

         DB::table('g_s_time_schedules')->insert([
            'id' => 5,
            'name' => 'Sesi 1',
            'day' => 'Tuesday',
            'start_time' => '10:00',
            'end_time' => '12:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 6,
            'name' => 'Sesi 2',
            'day' => 'Tuesday',
            'start_time' => '13:00',
            'end_time' => '15:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 7,
            'name' => 'Sesi 3',
            'day' => 'Tuesday',
            'start_time' => '16:00',
            'end_time' => '18:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 8,
            'name' => 'Sesi 4',
            'day' => 'Tuesday',
            'start_time' => '19:00',
            'end_time' => '21:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * Wednesday
         */

         DB::table('g_s_time_schedules')->insert([
            'id' => 9,
            'name' => 'Sesi 1',
            'day' => 'Wednesday',
            'start_time' => '10:00',
            'end_time' => '12:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 10,
            'name' => 'Sesi 2',
            'day' => 'Wednesday',
            'start_time' => '13:00',
            'end_time' => '15:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 11,
            'name' => 'Sesi 3',
            'day' => 'Wednesday',
            'start_time' => '16:00',
            'end_time' => '18:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 12,
            'name' => 'Sesi 4',
            'day' => 'Wednesday',
            'start_time' => '19:00',
            'end_time' => '21:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * Thursday
         */

         DB::table('g_s_time_schedules')->insert([
            'id' => 13,
            'name' => 'Sesi 1',
            'day' => 'Thursday',
            'start_time' => '10:00',
            'end_time' => '12:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 14,
            'name' => 'Sesi 2',
            'day' => 'Thursday',
            'start_time' => '13:00',
            'end_time' => '15:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 15,
            'name' => 'Sesi 3',
            'day' => 'Thursday',
            'start_time' => '16:00',
            'end_time' => '18:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 16,
            'name' => 'Sesi 4',
            'day' => 'Thursday',
            'start_time' => '19:00',
            'end_time' => '21:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * Friday
         */

         DB::table('g_s_time_schedules')->insert([
            'id' => 17,
            'name' => 'Sesi 1',
            'day' => 'Friday',
            'start_time' => '10:00',
            'end_time' => '12:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 18,
            'name' => 'Sesi 2',
            'day' => 'Friday',
            'start_time' => '13:00',
            'end_time' => '15:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 19,
            'name' => 'Sesi 3',
            'day' => 'Friday',
            'start_time' => '16:00',
            'end_time' => '18:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 20,
            'name' => 'Sesi 4',
            'day' => 'Friday',
            'start_time' => '19:00',
            'end_time' => '21:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * Saturday
         */

         DB::table('g_s_time_schedules')->insert([
            'id' => 21,
            'name' => 'Sesi 1',
            'day' => 'Saturday',
            'start_time' => '10:00',
            'end_time' => '12:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 22,
            'name' => 'Sesi 2',
            'day' => 'Saturday',
            'start_time' => '13:00',
            'end_time' => '15:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 23,
            'name' => 'Sesi 3',
            'day' => 'Saturday',
            'start_time' => '16:00',
            'end_time' => '18:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 24,
            'name' => 'Sesi 4',
            'day' => 'Saturday',
            'start_time' => '19:00',
            'end_time' => '21:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        /**
         * Sunday
         */

         DB::table('g_s_time_schedules')->insert([
            'id' => 25,
            'name' => 'Sesi 1',
            'day' => 'Sunday',
            'start_time' => '10:00',
            'end_time' => '12:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 26,
            'name' => 'Sesi 2',
            'day' => 'Sunday',
            'start_time' => '13:00',
            'end_time' => '15:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 27,
            'name' => 'Sesi 3',
            'day' => 'Sunday',
            'start_time' => '16:00',
            'end_time' => '18:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        DB::table('g_s_time_schedules')->insert([
            'id' => 28,
            'name' => 'Sesi 4',
            'day' => 'Sunday',
            'start_time' => '19:00',
            'end_time' => '21:30',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        }
}

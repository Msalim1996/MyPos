<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DbNumberCountersSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /**
         * Create super admin
         */
        $now = Carbon::now();

        DB::table('db_number_counters')->insert([
            'id' => 1,
            'type' => 'SO',
            'year' => $now->year,
            'month' => $now->month,
            'day' => null,
            'number' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('db_number_counters')->insert([
            'id' => 2,
            'type' => 'DO',
            'year' => $now->year,
            'month' => $now->month,
            'day' => null,
            'number' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('db_number_counters')->insert([
            'id' => 3,
            'type' => 'INV',
            'year' => $now->year,
            'month' => $now->month,
            'day' => null,
            'number' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('db_number_counters')->insert([
            'id' => 4,
            'type' => 'TO',
            'year' => $now->year,
            'month' => $now->month,
            'day' => null,
            'number' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('db_number_counters')->insert([
            'id' => 5,
            'type' => 'AO',
            'year' => $now->year,
            'month' => $now->month,
            'day' => null,
            'number' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}

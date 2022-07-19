<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BarcodeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('barcode_types')->insert([
            'prefix' => 'P',
            'type' => 'PUBLIC',
            'is_allowed_to_rent_shoe' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('barcode_types')->insert([
            'prefix' => 'S',
            'type' => 'STUDENT',
            'is_allowed_to_rent_shoe' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('barcode_types')->insert([
            'prefix' => 'C',
            'type' => 'CHAPERON',
            'is_allowed_to_rent_shoe' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('barcode_types')->insert([
            'prefix' => 'M',
            'type' => 'COMPLIMENT',
            'is_allowed_to_rent_shoe' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('barcode_types')->insert([
            'prefix' => 'V',
            'type' => 'VISITOR',
            'is_allowed_to_rent_shoe' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('barcode_types')->insert([
            'prefix' => 'O',
            'type' => 'OCA_PASS',
            'is_allowed_to_rent_shoe' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        DB::table('barcode_types')->insert([
            'prefix' => 'A',
            'type' => 'STAFF',
            'is_allowed_to_rent_shoe' => true,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

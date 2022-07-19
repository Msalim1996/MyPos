<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DefaultUserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        /**
         * Create super admin
         */

        DB::table('users')->insert([
            'id' => 1,
            'name' => 'IT Admin',
            'username' => 'A1337', // username is used as barcode id too
            'password' => bcrypt('admin123'),
            'position' => 'IT Admin',
            'date_join' => '2019-01-01',
            'date_left' => null,
            'starting_position' => 'IT Admin',
            'birthdate' => '1999-01-01',
            'gender' => 'Male',
            'religion' => 'Other',
            'address' => '',
            'phone' => '',
            'remark' => 'Default user',
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}


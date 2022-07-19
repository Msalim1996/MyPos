<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class PaymentMethodsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payment_methods')->insert([
            'id' => 1,
            'name' => 'Cash',
            'position_index' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
    }
}

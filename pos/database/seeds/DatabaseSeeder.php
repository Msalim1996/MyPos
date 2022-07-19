<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            DefaultUserTableSeeder::class,
            BarcodeTypeSeeder::class,
            DbNumberCountersSeeder::class,
            GeneralSettingSeeder::class,
            GSTimeScheduleSeeder::class,
            PaymentMethodsSeeder::class,
            RolesAndPermissionsSeeder::class,
            ShortcutTableSeeder::class
        ]);
    }
}

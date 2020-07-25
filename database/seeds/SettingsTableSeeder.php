<?php

use App\Setting;
use Illuminate\Database\Seeder;

class SettingsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::create([
            'client_id' => 1,
            'belt' => 1,
            'classes_til_stripe' => 30,
            'times_absent_til_contact' => 15,
        ]);

        Setting::create([
            'client_id' => 1,
            'belt' => 2,
            'classes_til_stripe' => 70,
            'times_absent_til_contact' => 15,
        ]);

        Setting::create([
            'client_id' => 1,
            'belt' => 3,
            'classes_til_stripe' => 80,
            'times_absent_til_contact' => 15,
        ]);

        Setting::create([
            'client_id' => 1,
            'belt' => 4,
            'classes_til_stripe' => 90,
            'times_absent_til_contact' => 15,
        ]);
        Setting::create([
            'client_id' => 2,
            'belt' => 1,
            'classes_til_stripe' => 30,
            'times_absent_til_contact' => 15,
        ]);

        Setting::create([
            'client_id' => 2,
            'belt' => 2,
            'classes_til_stripe' => 70,
            'times_absent_til_contact' => 15,
        ]);

        Setting::create([
            'client_id' => 2,
            'belt' => 3,
            'classes_til_stripe' => 80,
            'times_absent_til_contact' => 15,
        ]);

        Setting::create([
            'client_id' => 2,
            'belt' => 4,
            'classes_til_stripe' => 90,
            'times_absent_til_contact' => 15,
        ]);
    }
}

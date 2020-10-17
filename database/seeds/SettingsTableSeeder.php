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
            'belt_id' => 1,
            'sessions_til_stripe' => 30,
            'times_absent_til_contact' => 15,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 1,
            'belt_id' => 2,
            'sessions_til_stripe' => 70,
            'times_absent_til_contact' => 15,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 1,
            'belt_id' => 3,
            'sessions_til_stripe' => 80,
            'times_absent_til_contact' => 15,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 1,
            'belt_id' => 4,
            'sessions_til_stripe' => 90,
            'times_absent_til_contact' => 15,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 1,
            'belt_id' => 5,
            'sessions_til_stripe' => 0,
            'times_absent_til_contact' => 0,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 2,
            'belt_id' => 1,
            'sessions_til_stripe' => 30,
            'times_absent_til_contact' => 15,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 2,
            'belt_id' => 2,
            'sessions_til_stripe' => 70,
            'times_absent_til_contact' => 15,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 2,
            'belt_id' => 3,
            'sessions_til_stripe' => 80,
            'times_absent_til_contact' => 15,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 2,
            'belt_id' => 4,
            'sessions_til_stripe' => 90,
            'times_absent_til_contact' => 15,
            'combine_same_day_checkins' => true,
        ]);

        Setting::create([
            'client_id' => 2,
            'belt_id' => 5,
            'sessions_til_stripe' => 0,
            'times_absent_til_contact' => 0,
            'combine_same_day_checkins' => true,
        ]);
    }
}

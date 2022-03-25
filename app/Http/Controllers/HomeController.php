<?php

namespace App\Http\Controllers;

use App\Models\Day;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index() {

        $raw_settings =  Setting::where('client_id', Auth::user()->client_id)->get();
        $settings = [];
        $raw_settings->each(function($setting) use (&$settings) {

            $settings[$setting->belt_id] = [
                'id' => $setting->id,
                'client_id' => $setting->client_id,
                'sessions_til_stripe' => $setting->sessions_til_stripe,
                'weeks_absent_til_contact' => $setting->weeks_absent_til_contact,
                'combine_same_day_checkins' => $setting->combine_same_day_checkins,
            ];
        });

        return view('home', [
            'days' => Day::all(),
            'settings' => $settings,
        ]);
    }

    public function acceptToS() {

        $user = Auth::user();
        $user->tos = 1;
        $user->save();
    }
}

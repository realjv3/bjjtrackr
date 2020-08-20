<?php

namespace App\Http\Controllers;

use App\Day;
use App\Setting;
use App\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * HomeController constructor.
     */
    public function __construct() {
        $this->middleware('auth');
    }

    public function index() {

        $raw_settings =  Setting::where('client_id', Auth::user()->client_id)->get();
        $settings = [];
        $raw_settings->each(function($setting) use (&$settings) {

            $settings[$setting->belt] = [
                'id' => $setting->id,
                'client_id' => $setting->client_id,
                'sessions_til_stripe' => $setting->sessions_til_stripe,
                'times_absent_til_contact' => $setting->times_absent_til_contact,
                'combine_same_day_checkins' => $setting->combine_same_day_checkins,
            ];
        });

        return view('home', [
            'days' => Day::all(),
            'user' => User::with('roles')->find(Auth::id()),
            'settings' => $settings,
        ]);
    }
}

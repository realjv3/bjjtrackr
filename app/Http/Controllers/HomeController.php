<?php

namespace App\Http\Controllers;

use App\Setting;
use App\User;
use Illuminate\Http\Request;
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

        $raw_settings =  Setting::all();
        $settings = [];
        $raw_settings->each(function($setting) use (&$settings) {

            $settings[$setting->belt] = [
                'classes_til_stripe' => $setting->classes_til_stripe,
                'times_absent_til_contact' => $setting->times_absent_til_contact,
            ];
        });

        return view('home', [
            'user' => User::with('roles')->find(Auth::id()),
            'settings' => $settings,
        ]);
    }
}

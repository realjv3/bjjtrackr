<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function read()
    {
        $raw_settings = Setting::all();
        $settings = [];
        $raw_settings->each(function ($setting) use (&$settings) {

            $settings[$setting->belt] = [
                'classes_til_stripe' => $setting->classes_til_stripe,
                'times_absent_til_contact' => $setting->times_absent_til_contact,
            ];
        });
        return $settings;
    }


    public function update(Request $request)
    {
        $setting = Setting::find($request->input('belt'));
        $setting->update($request->all());
        return $this->read();
    }
}

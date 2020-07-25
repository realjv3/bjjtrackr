<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function read($client_id)
    {
        $raw_settings = Setting::where('client_id', $client_id)->get();
        $settings = [];
        $raw_settings->each(function ($setting) use (&$settings) {

            $settings[$setting->belt] = [
                'id' => $setting->id,
                'client_id' => $setting->client_id,
                'classes_til_stripe' => $setting->classes_til_stripe,
                'times_absent_til_contact' => $setting->times_absent_til_contact,
            ];
        });
        return $settings;
    }


    public function update($id, Request $request)
    {
        if (Gate::allows('isSuperAdmin') || Gate::allows('isAdmin')) {
            $setting = Setting::find($id);
            $setting->update($request->all());
        }
        return $this->read($request->client_id);
    }
}

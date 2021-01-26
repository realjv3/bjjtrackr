<?php

namespace App\Http\Controllers;

use App\Setting;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function read($client_id)
    {
        $raw_settings = Setting::where('client_id', $client_id)->get()->toArray();
        $settings = [];
        for ($i = 0; $i < count($raw_settings); $i++) {

            $settings[$raw_settings[$i]['belt_id']] = [
                'id' => $raw_settings[$i]['id'],
                'client_id' => $raw_settings[$i]['client_id'],
                'sessions_til_stripe' => $raw_settings[$i]['sessions_til_stripe'],
                'weeks_absent_til_contact' => $raw_settings[$i]['weeks_absent_til_contact'],
                'combine_same_day_checkins' => $raw_settings[$i]['combine_same_day_checkins'],
            ];
        }
        return $settings;
    }


    public function update($id, Request $request)
    {
        if (Gate::allows('isSuperAdmin') || Gate::allows('isAdmin')) {
            $request->validate([
                'sessions_til_stripe' => 'numeric|min:1|max:255',
                'weeks_absent_til_contact' => 'numeric|min:1|max:255',
                'combine_same_day_checkins' => 'boolean'
            ]);
            $setting = Setting::find($id);
            $setting->update($request->all());
            return $this->read(['success' => true]);
        }
        return $this->read(['success' => false]);
    }
}

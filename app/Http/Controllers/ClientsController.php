<?php

namespace App\Http\Controllers;

use App\Client;
use App\Rank;
use App\Setting;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ClientsController extends Controller
{
    public function read() {

        if (Gate::allows('isSuperAdmin')) {
            return Client::all();
        } else {
            $user = Auth::user();
            $client = $user->client;
            return Client::with(['users'])->where('id', $client->id)->get();
        }
    }

    public function create(Request $request) {

        $request->validate(['name' => 'required']);

        $client = new Client($request->all());
        $client->save();

        $this->insertSettings($client->id);
    }

    public function update(Request $request, $id) {

        $request->validate(['name' => 'required']);

        $client = Client::find($id);
        $client->update($request->all());
        return $client;
    }

    public function delete($id) {

        Client::destroy($id);
    }

    /**
     * Inserts initial settings for passed client
     *
     * @param int $client_id
     */
    private function insertSettings(int $client_id) {

        Setting::create([
            'client_id' => $client_id,
            'belt_id' => 1,
            'sessions_til_stripe' => 30,
            'weeks_absent_til_contact' => 3,
            'combine_same_day_checkins' => 1,
        ]);

        Setting::create([
            'client_id' => $client_id,
            'belt_id' => 2,
            'sessions_til_stripe' => 70,
            'weeks_absent_til_contact' => 3,
            'combine_same_day_checkins' => 1,
        ]);

        Setting::create([
            'client_id' => $client_id,
            'belt_id' => 3,
            'sessions_til_stripe' => 80,
            'weeks_absent_til_contact' => 3,
            'combine_same_day_checkins' => 1,
        ]);

        Setting::create([
            'client_id' => $client_id,
            'belt_id' => 4,
            'sessions_til_stripe' => 90,
            'weeks_absent_til_contact' => 3,
            'combine_same_day_checkins' => 1,
        ]);

        Setting::create([
            'client_id' => $client_id,
            'belt_id' => 5,
            'sessions_til_stripe' => 0,
            'weeks_absent_til_contact' => 0,
            'combine_same_day_checkins' => 1,
        ]);
    }
}

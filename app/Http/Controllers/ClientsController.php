<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClientsController extends Controller
{
    public function read() {

        if (Gate::allows('isSuperAdmin')) {

            $clients =  Client::all();

            return $clients->map(function($client) {
                $activeMembers = $client->members()->where('status', 'canceled')->where('pause_collection', 0)->count();
                return [...$client->toArray(), 'activeMembers' => $activeMembers];
            });

        } else {
            $user = Auth::user();
            $client = $user->client;
            return [Client::find($client->id)];
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

<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Rank;
use App\Models\Setting;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    public function signup(Request $request) {

        $request->validate([
            'client.id' => Rule::requiredIf(! empty($request->person['id'])),
            'client.name' => 'required',
            'person.id' => Rule::requiredIf(! empty($request->client['id'])),
            'person.name' => 'required',
            'person.email' => 'required|email',
            'person.phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'person.rank.belt_id' => 'required|numeric',
            'person.rank.stripes' => 'required|numeric',
            'person.start_date' => 'nullable|date',
            'person.rank.last_ranked_up' => 'nullable|date',
        ]);

        $this->validate($request, [
            'person.email' => [
                function ($attribute, $value, $fail) {
                    if (User::where('email', $value)->count() > 0) {
                        $fail($value .' is already in use.');
                    }
                }
            ]
        ]);

        $creating = ! Auth::check();

        if ($creating) {

            $request->validate(['person.password' => 'required|confirmed']);
            $validator = Validator::make(['email' => $request->person['email']], ['unique:users']);
            $validator->validate();

            $client = new Client($request->client);
            $client->save();

            $this->insertSettings($client->id);

            $user = new User([
                'name' => $request->person['name'],
                'email' => $request->person['email'],
                'password' => bcrypt($request->person['password']),
                'start_date' => empty($request->person['start_date']) ? date('Y-m-d') : $request->person['start_date'],
            ]);
            $user->client()->associate($client->id);
            $user->save();
            $user->roles()->attach(2);
            $rank = new Rank([
                'user_id' => $user->id,
                'belt_id' => $request->person['rank']['belt_id'],
                'stripes' => $request->person['rank']['stripes'],
                'last_ranked_up' => date('Y-m-d'),
            ]);
            $rank->save();
        } else {
            $request->validate(['person.password' => 'confirmed']);

            $client = Client::find($request->client['id']);
            $user = User::find($request->person['id']);
            $rank = Rank::find($request->person['rank']['id']);

            if ($request->email != $user['email']) {

                $request->validate(['email' => 'unique:users']);
            }

            $client->update($request->client);
            $userProps = collect($request->person);
            $user->update($userProps->except('password')->toArray());
            if ( ! empty($request->person['password'])) {

                $user->password = Hash::make($userProps['password']);
                $user->save();
            }
            $rank->update($userProps['rank']);
        }

        if ($creating) {
            Auth::loginUsingId($user->id);
        }

        return ['success' => true, 'client' => $client, 'person' => $user];
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

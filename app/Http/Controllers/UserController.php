<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Rank;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Stripe\StripeClient;

class UserController extends Controller
{
    public function read($client_id = null) {

        if (Gate::allows('isSuperAdmin')) {
            if ( ! empty($client_id)) {
                return User::with(['rank.belt', 'roles', 'client', 'lastCheckin'])
                    ->where('client_id', $client_id)
                    ->orderBy('name')
                    ->get();
            } else {
                return User::with(['rank.belt', 'roles', 'client', 'lastCheckin'])
                    ->orderBy('name')
                    ->get();
            }
        } else {
            $user = Auth::user();
            $clientId = $user->client_id;
            return User::with(['rank.belt', 'client', 'roles', 'lastCheckin'])
                ->where('client_id', $clientId)
                ->orderBy('name')
                ->get();
        }
    }

    public function getLoggedInUser() {
        $user = Auth::user();
        $userId = $user->id;
        return User::with(['rank.belt', 'client', 'roles', 'lastCheckin'])
            ->where('id', $userId)
            ->get();
    }

    public function create(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'phone' => 'required|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'password' => 'required|confirmed',
            'roles' => 'required|array',
            'rank.belt_id' => 'required|numeric',
            'rank.stripes' => 'required|numeric',
            'client_id' => Rule::requiredIf( ! in_array(1, $request->roles ? $request->roles : [])),
            'start_date' => 'nullable|date',
            'rank.last_ranked_up' => 'nullable|date',
            'active' => 'required|boolean',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'notes' => $request->notes,
            'start_date' => $request->start_date,
            'active' => $request->active,
        ]);
        if ( ! empty($request->client_id)) {
            $user->client()->associate($request->client_id);
        }
        $user->save();

        if ( ! empty($request->roles)) {
            $user->roles()->attach($request->roles);
        }

        if ( ! empty($request->rank)) {

            $rank = new Rank([
                'user_id' => $user->id,
                'belt_id' => $request->rank['belt_id'],
                'stripes' => $request->rank['stripes'],
                'last_ranked_up' => date('Y-m-d'),
            ]);
            $rank->save();
        }
    }

    public function update(Request $request, $id, StripeClient $stripe) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'phone' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10',
            'password' => 'confirmed',
            'roles' => 'required|array',
            'rank.belt_id' => 'required|numeric',
            'rank.stripes' => 'required|numeric',
            'rank.last_ranked_up' => 'nullable|date',
            'client_id' => Rule::requiredIf( ! in_array(1, $request->roles ? $request->roles : [])),
            'start_date' => 'nullable|date',
            'active' => 'required|boolean',
        ]);

        $user = User::find($id);

        if ($request->email != $user->email) {

            $request->validate(['email' => 'unique:users']);

            // if changing email for client's first admin, also change it on stripe
            $client = Client::find($user->client_id);
            $firstAdmin = $client->getFirstAdmin();
            if ($firstAdmin->id == $user->id) {

                $subscription = Subscription::where('client_id', $user->client_id)->first();
                $stripe->customers->update($subscription->cust_id, ['email' => $request->email]);
            }
        }

        if ( ! empty($request->client_id)) {

            $user->client()->associate($request->client_id);
        } else {
            $user->client()->disassociate();
        }

        if ( ! empty($request->password)) {
            if (
                Gate::forUser($user)->allows('isSuperAdmin')
                && Gate::denies('isSuperAdmin')
            ) {
            // non super-admins not allowed to change super-admins passwords
            } else {
                $user->password = Hash::make($request->password);
                $user->save();
            }
        }

        $user->update($request->except('password'));

        if ( ! empty($request->roles)) {

            $user->roles()->sync($request->roles);
        }

        if ( ! empty($request->rank)) {

            $rank = Rank::where('user_id', $request->id)->get()->first();
            $rank->belt_id = $request->rank['belt_id'];
            $rank->stripes = $request->rank['stripes'];
            $rank->last_ranked_up = $request->rank['last_ranked_up'];
            $rank->save();
        }
    }

    public function delete($id) {

        $user = User::find($id);
        $user->roles()->detach();
        Storage::delete('public/qrcodes/' . $id . '_qrcode.png');
        $user->delete();
    }

    public function getQrCode(\BaconQrCode\Writer $writer, Request $request, $id) {

        if ( ! is_dir(public_path() . '/storage/qrcodes')) {
            mkdir(public_path() . '/storage/qrcodes', 0755);
        }
        $fileName = public_path() . '/storage/qrcodes/' . $id . '_qrcode.png';
        if ( ! Storage::exists($fileName)) {
            $writer->writeFile($id, $fileName);
        }
        $type = File::mimeType($fileName);
        return response()->file($fileName, ["Content-Type" => $type]);
    }

    public function toured() {

        $user = Auth::user();
        $user->toured = 1;
        $user->save();
    }
}

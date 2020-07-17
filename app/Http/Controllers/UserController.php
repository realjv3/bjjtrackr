<?php

namespace App\Http\Controllers;

use App\Rank;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
    }

    public function read($client_id = null) {

        if (Gate::allows('isSuperAdmin')) {
            if ( ! empty($client_id)) {
                return User::with(['rank', 'roles', 'client'])
                    ->where('client_id', $client_id)
                    ->orderBy('name')
                    ->get();
            } else {
                return User::with(['rank', 'roles', 'client'])
                    ->orderBy('name')
                    ->get();
            }
        } else {
            $user = Auth::user();
            $clientId = $user->client_id;
            return User::with(['rank', 'client', 'roles'])
                ->where('client_id', $clientId)
                ->orderBy('name')
                ->get();
        }
    }

    public function create(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed',
            'roles' => 'required|array',
            'rank.belt' => 'required|numeric',
            'rank.stripes' => 'required|numeric',
            'client_id' => Rule::requiredIf( ! in_array(1, $request->roles ? $request->roles : [])),
            'start_date' => 'nullable|date',
            'rank.last_ranked_up' => 'nullable|date',
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'belt' => $request->belt,
            'stripes' => $request->stripes,
            'notes' => $request->notes,
            'start_date' => $request->start_date,
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
                'belt' => $request->rank['belt'],
                'stripes' => $request->rank['stripes'],
                'last_ranked_up' => date('Y-m-d'),
            ]);
            $rank->save();
        }
    }

    public function update(Request $request, $id) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'confirmed',
            'roles' => 'required|array',
            'rank.belt' => 'required|numeric',
            'rank.stripes' => 'required|numeric',
            'rank.last_ranked_up' => 'nullable|date',
            'client_id' => Rule::requiredIf( ! in_array(1, $request->roles ? $request->roles : [])),
            'start_date' => 'nullable|date',
        ]);

        $user = User::find($id);

        if ( ! empty($request->client_id)) {

            $user->client()->associate($request->client_id);
        } else {
            $user->client()->disassociate();
        }

        $user->update($request->all());

        if ( ! empty($request->password)) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        if ( ! empty($request->roles)) {

            $user->roles()->sync($request->roles);
        }

        if ( ! empty($request->rank)) {

            $rank = Rank::where('user_id', $request->id)->get()->first();
            $rank->belt = $request->rank['belt'];
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
}

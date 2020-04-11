<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
    }

    public function read($client_id = null) {

        if (Gate::allows('isSuperAdmin')) {
            if ( ! empty($client_id)) {
                return User::with(['roles', 'client'])
                    ->where('client_id', $client_id)
                    ->orderBy('name')
                    ->get();
            } else {
                return User::with(['roles', 'client'])
                    ->orderBy('name')
                    ->get();
            }
        } else {
            $user = Auth::user();
            $client = $user->client;
            return User::with(['client', 'roles'])
                ->where('client_id', $client->id)
                ->orderBy('name')
                ->get();
        }
    }

    public function create(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed',
            'roles' => 'required|numeric',
            'belt' => 'required|numeric',
            'stripes' => 'required|numeric',
            'client_id' => Rule::requiredIf( ! in_array(1, $request->roles ? $request->roles : [])),
            'start_date' => 'nullable|date',
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
    }

    public function update(Request $request, $id) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'confirmed',
            'roles' => 'required|numeric',
            'belt' => 'required|numeric',
            'stripes' => 'required|numeric',
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

        if ( ! empty($request->roles)) {

            $user->roles()->sync($request->roles);
        }
    }

    public function delete($id) {

        $user = User::find($id);
        $user->roles()->detach();
        $user->delete();
    }

    public function getQrCode(\BaconQrCode\Writer $writer, Request $request, $id) {
        $fileName = $id . '_qrcode.png';
        $writer->writeFile($id, $fileName);
        $path = public_path() . '/' . $fileName;
        $type = File::mimeType($path);
        return response()->file($path, ["Content-Type" => $type]);
    }
}

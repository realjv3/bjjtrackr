<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
    }

    public function read() {

        $user = Auth::user();
        $isSuperAdmin = false;
        $roles = $user->roles;
        $client = $user->client;

        foreach ($roles as $role) {
            if ($role->role == 'Super Admin') {
                $isSuperAdmin = true;
                break;
            }
        }

        if ($isSuperAdmin) {
            return User::with(['roles', 'client'])->get();
        } else {
            return User::with(['client', 'roles'])->where('client_id', $client->id)->get();
        }
    }

    public function create(Request $request) {

        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users|email',
            'password' => 'required|confirmed',
            'roles' => 'required',
            'belt' => 'required',
            'stripes' => 'required',
            'client_id' => Rule::requiredIf( ! in_array(1, $request->roles ? $request->roles : [])),
        ]);

        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'belt' => $request->belt,
            'stripes' => $request->stripes,
            'notes' => $request->notes,
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
            'roles' => 'required',
            'belt' => 'required',
            'stripes' => 'required',
            'client_id' => Rule::requiredIf( ! in_array(1, $request->roles ? $request->roles : [])),
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
}

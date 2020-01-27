<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClientsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function read() {

        $user = Auth::user();
        $isSuperAdmin = false;
        $roles = $user->roles;

        foreach ($roles as $role) {
            if ($role->role == 'Super Admin') {
                $isSuperAdmin = true;
                break;
            }
        }

        if ($isSuperAdmin) {
            return Client::all();
        } else {
            return [Client::find($user->client->id)];
        }
    }

    public function create(Request $request) {

        $request->validate(['name' => 'required']);

        $client = new Client($request->all());
        $client->save();
    }

    public function update(Request $request, $id) {

        $request->validate(['name' => 'required']);

        $client = Client::find($id);
        $client->update($request->all());
    }

    public function delete($id) {

        Client::destroy($id);
    }
}

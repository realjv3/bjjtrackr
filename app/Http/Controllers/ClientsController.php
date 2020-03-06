<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ClientsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

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

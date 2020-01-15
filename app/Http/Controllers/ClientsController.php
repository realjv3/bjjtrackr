<?php

namespace App\Http\Controllers;

use App\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    public function __construct() {
        $this->middleware('auth');
    }

    public function read() {

        return Client::all();
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

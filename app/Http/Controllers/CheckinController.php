<?php

namespace App\Http\Controllers;

use App\Checkin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class CheckinController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
    }

    public function read(Request $request,  $clientId = null) {

        if (Gate::allows('isStudentOnly')) {

            $user = Auth::user();
            return Checkin::with(['user'])
                ->where('user_id', $user->id)
                ->orderBy('checked_in_at')
                ->get();

        } else if (Gate::allows('isSuperAdmin')) {

            if ( ! empty($clientId)) {
                return Checkin::with(['user'])
                    ->where('client_id', $clientId)
                    ->orderBy('checked_in_at')
                    ->get();
            } else {
                return Checkin::with(['user'])->orderBy('checked_in_at')->get();
            }
        } else {
            $user = Auth::user();
            $client = $user->client;
            return Checkin::with(['user'])
                ->orderBy('checked_in_at')
                ->where('client_id', $client->id)
                ->get();
        }
    }

    public function create(Request $request) {

        $request->validate([
            'client_id' => 'required|integer',
            'user_id' => 'required|integer',
            'checked_in_at' => 'date_format:Y-m-d H:i:s',
        ]);

        $checkin = new Checkin();
        $checkin->client()->associate($request->client_id);
        $checkin->user()->associate($request->user_id);
        $checkin->checked_in_at = ! empty($request->checked_in_at) ? $request->checked_in_at : gmdate('Y-m-d H:i:s');
        $checkin->save();

        return $checkin;
    }

    public function update(Request $request, $id) {

        $request->validate([
            'client_id' => 'required|integer',
            'user_id' => 'required|integer',
            'checked_in_at' => 'required|date_format:Y-m-d H:i:s',
        ]);

        $checkin = Checkin::find($id);
        $checkin->update($request->all());

        return $checkin;
    }

    public function delete($id) {

        $checkin = Checkin::find($id);
        $checkin->delete();
    }
}

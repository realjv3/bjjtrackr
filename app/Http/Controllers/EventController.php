<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function __construct() {

        $this->middleware('auth');
    }

    public function create(Request $request) {

        $request->validate([
            'client_id' => 'required|integer',
            'day_id' => 'required|integer',
            'name' => 'required|string',
            'start' => 'required|date_format:H:i',
            'end' => 'required|date_format:H:i',
        ]);

        $event = new Event();
        $event->client()->associate($request->client_id);
        $event->day_id = $request->day_id;
        $event->name = $request->name;
        $event->start = $request->start;
        $event->end = $request->end;
        $event->save();

        return $event;
    }

    public function read($client_id, $event_id = null) {

        if ( ! empty($event_id)) {
            return Event::find($event_id);
        } else {
            return Event::where('client_id', $client_id)
                ->orderBy('day_id')
                ->get();
        }
    }

    public function update(Request $request, $id) {

        $request->validate([
            'client_id' => 'required|integer',
            'day_id' => 'required|integer',
            'name' => 'required|string',
            'start' => 'required|string',
            'end' => 'required|string',
        ]);

        $event = Event::find($id);
        $event->update($request->all());

        return $event;
    }

    public function delete($id) {

        $event = Event::find($id);
        $event->delete();

        return ['success' => true];
    }
}

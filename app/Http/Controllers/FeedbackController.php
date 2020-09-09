<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mailgun\Mailgun;

class FeedbackController extends Controller
{
    public function create(Request $request) {

        $name = Auth::user()->name;
        $email = Auth::user()->email;
        $domain = config('services.mailgun.domain');
        $apiKey = config('services.mailgun.api_key');
        $url = config('services.mailgun.endpoint');
        $to = config('mail.from.address');

        $mg = Mailgun::create($apiKey, $url);
        $mg->messages()->send($domain, [
            'from' => $name . ' <' . $email . '>',
            'to' => 'BJJTrackr Support <' . $to . '>',
            'subject' => 'BJJTrackr Feedback',
            'text'	=> $request->message,
        ]);
    }
}

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
        $domain = config('mail.mailgun.domain');
        $apiKey = config('mail.mailgun.api_key');
        $url = config('mail.mailgun.url');
        $to = config('mail.mailgun.to');

        $mg = Mailgun::create($apiKey, $url);
        $mg->messages()->send($domain, [
            'from' => $name . ' <' . $email . '>',
            'to' => 'BJJTrackr Support <' . $to . '>',
            'subject' => 'BJJTrackr Feedback',
            'text'	=> $request->message,
        ]);
    }
}

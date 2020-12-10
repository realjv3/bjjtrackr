<?php

namespace App\Http\Controllers;

use App\Mail\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class FeedbackController extends Controller
{
    public function create(Request $request) {

        Mail::to(config('mail.from.address'))->send(new Feedback($request->message));
    }
}

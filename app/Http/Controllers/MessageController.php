<?php

namespace App\Http\Controllers;

use App\Mail\EligibleForPromo;
use App\Mail\Feedback;
use App\Rank;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MessageController extends Controller
{
    public function feedback(Request $request) {

        Mail::to(config('mail.from.address'))->send(new Feedback($request->message));
    }

    public function eligibleForPromo(Request $request) {

        $user = User::with('rank')->where('id', $request->userId)->first();
        $adminEmail = DB::table('users')
            ->join('user_role', 'users.id', '=', 'user_role.user_id')
            ->where('users.client_id', '=', $user->client_id)
            ->where('user_role.role_id', '=', 2)
            ->value('email');
        Mail::to($adminEmail)
            ->bcc(config('mail.from.address'))
            ->send(new EligibleForPromo($user->name, Rank::find($user->rank->id)));
    }
}

<?php

namespace App\Logger;

use App\Log;
use Illuminate\Support\Facades\Auth;

class Logger
{
    static public function log(string $type, string $action) {

        $user = Auth::user();
        $log = new Log([
            'client_id' => $user ? $user->client_id : null,
            'user_id' => $user ? $user->id : null,
            'type' => $type,
            'action' => $action,
        ]);
        $log->save();
    }
}

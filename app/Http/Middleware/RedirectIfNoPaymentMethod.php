<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Gate;

class RedirectIfNoPaymentMethod
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Gate::allows('isAdmin') && Gate::denies('subscriptionActive')) {

            return redirect()->route('payment_method');
        }

        return $next($request);
    }
}

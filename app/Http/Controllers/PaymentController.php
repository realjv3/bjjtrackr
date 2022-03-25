<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Stripe\Account;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    /**
     * PaymentController constructor.
     * @param StripeClient $stripe
     */
    public function __construct(private StripeClient $stripe) {
    }

    /**
     * Stripe Connect onboarding for client
     *
     * @param Request $request
     * @param Client $client
     * @param StripeClient $stripeClient
     */
    public function stripeConnect(Request $request, Client $client) {

        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin') ) {

            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        if (empty($client->stripe_account)) {

            $account = $this->stripe->accounts->create([
                'type' => 'standard',
                'email' => $request->input('email'),
            ]);

            $client->stripe_account = $account->id;
            $client->save();
        }

        $link = $this->stripe->accountLinks->create([
            'account' => $client->stripe_account,
            'refresh_url' => config('app.url') . 'stripeconnect/' . $client->id,
            'return_url' => config('app.url'),
            'type' => 'account_onboarding',
        ]);


        return ['url' => $link->url];
    }

    /**
     * Gets connected Stripe account of a client
     *
     * @param Request $request
     * @param Client $client
     * @param StripeClient $stripeClient
     */
    public function stripeConnectAccount(Request $request, Client $client) {

        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin') ) {

            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        if (empty($client->stripe_account)) {

            return response()->json(['error' => 'Stripe account not found.'], 404);
        }

        $stripeAccount = $this->stripe->accounts->retrieve($client->stripe_account, []);
        $this->updateChargesEnabled($client, $stripeAccount);

        return ['stripe_account' => $stripeAccount];
    }

    /**
     * Sets Client->membership field
     *
     * @param Client $client
     */
    private function updateChargesEnabled(Client $client, Account $stripeAccount) {

        $client->charges_enabled = $stripeAccount->charges_enabled;
        $client->save();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Stripe\StripeClient;

class PaymentMethodsController extends Controller
{
    /**
     * PaymentController constructor.
     * @param StripeClient $stripe
     */
    public function __construct(private StripeClient $stripe) {}

    /**
     * Gets all the member's (Stripe customer) payment methods
     *
     * @param Client $client
     * @param User $user
     *
     * @return array|JsonResponse
     */
    public function getPaymentMethods(Client $client, User $user) {

        if ((Gate::denies('isAdmin') && Gate::denies('isSuperAdmin')) || $user->client_id !== $client->id) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $paymentMethods = collect();
        $member = $user->members->first();
        $defaultPaymentMethod = null;
        if ( ! empty($member)) {
            $custId = $member->cust_id;
            $customer = $this->stripe->customers->retrieve($custId, null, ["stripe_account" => $client->stripe_account]);
            $paymentMethods = $this->stripe->paymentMethods->all(
                ['customer' => $custId, 'type' => 'card'],
                ["stripe_account" => $client->stripe_account]
            );
            $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;
        }

        return [
            ...$paymentMethods->toArray(),
            'default_payment_method' => $defaultPaymentMethod,
        ];
    }
    /**
     * Sets Stripe default payment method
     *
     * @param Request $request
     * @param Client $client
     * @param User $user
     *
     * @return JsonResponse
     */
    public function setDefaultPaymentMethod(Request $request, Client $client, User $user) {

        if ((Gate::denies('isAdmin') && Gate::denies('isSuperAdmin')) || $user->client_id !== $client->id) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'custId' => 'string|required',
            'paymentMethodId' => 'string|required',
        ]);

        $custId = $request->custId;

        $payment_method = $this->stripe->paymentMethods->retrieve(
            $request->paymentMethodId,
            null,
            ["stripe_account" => $client->stripe_account]
        );
        $payment_method->attach(['customer' => $custId], ["stripe_account" => $client->stripe_account]);

        // Set the default payment method on the customer
        $this->stripe->customers->update($custId, [
            'invoice_settings' => ['default_payment_method' => $request->paymentMethodId],
        ], ["stripe_account" => $client->stripe_account]);

        return $this->getPaymentMethods($client, $user);
    }


    /**
     * Detaches a given payment method
     *
     * @param Request $request
     * @param Client $client
     * @param User $user
     *
     * @return array|JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function deletePaymentMethod(Request $request, Client $client, User $user) {

        if ((Gate::denies('isAdmin') && Gate::denies('isSuperAdmin')) || $user->client_id !== $client->id) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate(['paymentMethodId' => 'string|required']);

        $this->stripe->paymentMethods->detach(
            $request->paymentMethodId,
            [],
            ["stripe_account" => $client->stripe_account]
        );

        return ['paymentMethodId' => $request->paymentMethodId];
    }
}

<?php

namespace App\Http\Controllers;

use App\Facades\PaymentMethods;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PaymentMethodsController extends Controller
{
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

        list($paymentMethods, $defaultPaymentMethod) = PaymentMethods::getAllPaymentMethods($client, $user);

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
     * @return array|JsonResponse
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
        $paymentMethodId = $request->paymentMethodId;

        PaymentMethods::setDefault($client, $paymentMethodId, $custId);

        return ['paymentMethodId' => $paymentMethodId];
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

        $paymentMethodId = $request->paymentMethodId;

        PaymentMethods::detach($client, $paymentMethodId);

        return ['paymentMethodId' => $paymentMethodId];
    }
}

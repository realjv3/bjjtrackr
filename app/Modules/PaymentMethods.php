<?php

namespace App\Modules;

use App\Models\Client;
use App\Models\User;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class PaymentMethods
{
    /**
     * PaymentController constructor.
     * @param StripeClient $stripe
     */
    public function __construct(private StripeClient $stripe) {}

    /**
     * Gets all payment methods for a given user
     *
     * @param User $user
     * @param Client $client
     *
     * @return array
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getAllPaymentMethods(Client $client, User $user): array
    {
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

        return [$paymentMethods, $defaultPaymentMethod];
    }

    /**
     * Sets the default payment method for a given Stripe customer
     *
     * @param Client $client
     * @param string $paymentMethodId
     * @param string $custId
     *
     * @throws ApiErrorException
     * @return void
     */
    public function setDefault(Client $client, string $paymentMethodId, string $custId): void
    {
        $payment_method = $this->stripe->paymentMethods->retrieve(
            $paymentMethodId,
            null,
            ["stripe_account" => $client->stripe_account]
        );
        $payment_method->attach(['customer' => $custId], ["stripe_account" => $client->stripe_account]);

        // Set the default payment method on the customer
        $this->stripe->customers->update($custId, [
            'invoice_settings' => ['default_payment_method' => $paymentMethodId],
        ], ["stripe_account" => $client->stripe_account]);
    }

    /**
     * Detaches a given payment method
     *
     * @param Client $client
     * @param string $paymentMethodId
     *
     * @throws ApiErrorException
     */
    public function detach(Client $client, string $paymentMethodId)
    {
        $this->stripe->paymentMethods->detach(
            $paymentMethodId,
            [],
            ["stripe_account" => $client->stripe_account]
        );
    }
}

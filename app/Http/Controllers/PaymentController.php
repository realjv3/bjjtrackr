<?php

namespace App\Http\Controllers;

use App\Subscription;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    /**
     * @var $stripe StripeClient
     */
    private $stripe;

    /**
     * PaymentController constructor.
     * @param StripeClient $stripe
     */
    public function __construct(StripeClient $stripe) {

        $this->stripe = $stripe;
    }

    /**
     * Finds or creates a Stripe customer
     *
     * @param Request $request
     * @return \Stripe\Customer|string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function findOrCreateCustomer(Request $request) {

        if (Gate::denies('isAdmin')) {
            http_response_code(401);
            return 'Unauthorized';
        }

        $user = Auth::user();
        $subscription = Subscription::where(['user_id' => $user->id])->first();

        if ( ! empty($subscription) && ! empty($subscription->cust_id)) {

            $customer = $this->stripe->customers->retrieve($subscription->cust_id);
        } else {
            $customer = $this->stripe->customers->create([
                'name' => $user->name,
                'email' => $user->email,
                'metadata' => ['user_id' => $user->id],
            ]);
            $subscription = new Subscription(['user_id' => $user->id, 'cust_id' => $customer->id]);
            $subscription->save();
        }

        return $customer;
    }

    /**
     * Attaches Stripe customer to Stripe payment method, then sets it as default payment method;
     * Upserts Stripe subscription;
     *
     * @param Request $request
     * @return string|\Stripe\Subscription
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function upsertSubscription(Request $request) {

        if (Gate::denies('isAdmin')) {
            http_response_code(401);
            return 'Unauthorized';
        }

        $request->validate([
            'custId' => 'string|required',
            'paymentMethodId' => 'string|required',
        ]);

        $sub = Subscription::where(['user_id' => Auth::user()->id, 'cust_id' => $request->custId])->first();

        if (empty($sub)) {
            http_response_code(404);
            return 'Subscription record not found.';
        }

        $payment_method = $this->stripe->paymentMethods->retrieve($request->paymentMethodId);
        $payment_method->attach(['customer' => $request->custId]);

        // Set the default payment method on the customer
        $this->stripe->customers->update($request->custId, [
            'invoice_settings' => [
                'default_payment_method' => $request->paymentMethodId,
            ],
        ]);

        if ( ! empty($sub) && ! empty($sub->subscription_id)) {

            $subscription = $this->stripe->subscriptions->retrieve($sub->subscription_id);
        } else {
            // Create the subscription
            $subscription = $this->stripe->subscriptions->create([
                'customer' => $request->custId,
                'items' => [['price' => 'price_1HQEPnAdSpfz7pjghEYUL7FD']],
                'expand' => ['latest_invoice.payment_intent'],
            ]);
            $sub->subscription_id = $subscription->id;
            $sub->item_id = $subscription->items->data[0]->id;
            $sub->current_period_end = $subscription->current_period_end;
            $sub->price_id = $subscription->items->data[0]->price->id;
            $sub->status = $subscription->status;
            $sub->save();
        }

        return $subscription;
    }

    public function handle(Request $request) {

        switch ($request->type) {
            case 'invoice.paid':
                // The status of the invoice will show up as paid. Store the status in your
                // database to reference when a user accesses your service to avoid hitting rate
                // limits.
                Log::info('Stripe Webhook ' . $request->type . $request->all());
                break;
            case 'invoice.payment_failed':
                // If the payment fails or the customer does not have a valid payment method,
                // an invoice.payment_failed event is sent, the subscription becomes past_due.
                // Use this webhook to notify your user that their payment has
                // failed and to retrieve new card details.
                Log::info('Stripe Webhook ' . $request->type . $request->all());
                break;
            default:
                // Unhandled event type
                Log::info('Stripe Webhook ' . $request->type . $request->all());
        }

        return ['msg' => $request->all()];
    }
}

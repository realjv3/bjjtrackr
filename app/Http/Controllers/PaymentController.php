<?php

namespace App\Http\Controllers;

use App\Mail\ProcessedPayment;
use App\Mail\Welcome;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Exception\SignatureVerificationException;
use Stripe\StripeClient;
use Stripe\Webhook;

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
        $subscription = Subscription::where(['client_id' => $user->client->id])->first();

        if ( ! empty($subscription) && ! empty($subscription->cust_id)) {

            $customer = $this->stripe->customers->retrieve($subscription->cust_id);
        } else {
            $customer = $this->stripe->customers->create([
                'name' => $user->name,
                'email' => $user->email,
                'metadata' => ['client_id' => $user->client->id],
            ]);
            $subscription = new Subscription(['client_id' => $user->client->id, 'cust_id' => $customer->id]);
            $subscription->save();
        }

        return $customer;
    }

    /**
     * Finds or creates a Stripe customer
     *
     * @param Request $request
     * @return \Stripe\Customer|string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function updateCustomer(Request $request) {

        if (Gate::denies('isAdmin')) {
            http_response_code(401);
            return 'Unauthorized';
        }

        $user = Auth::user();
        $subscription = Subscription::where(['client_id' => $user->client->id])->first();

        if ( ! empty($subscription) && ! empty($subscription->cust_id)) {

            $customer = $this->stripe->customers->update($subscription->cust_id, ['email']);
        }

        return $customer;
    }

    /**
     * Gets all of the Stripe customer's payment methods
     *
     * @return array|string
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getPaymentMethods() {

        if (Gate::denies('isAdmin')) {
            http_response_code(401);
            return 'Unauthorized';
        }

        $paymentMethods = collect();
        $sub = Subscription::where(['client_id' => Auth::user()->client->id])->get()->first();
        $defaultPaymentMethod = null;
        if ( ! empty($sub)) {
            $custId = $sub->cust_id;
            $customer = $this->stripe->customers->retrieve($custId);
            $paymentMethods = $this->stripe->paymentMethods->all(['customer' => $custId, 'type' => 'card']);
            $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;
        }

        return array_merge(
            $paymentMethods->toArray(),
            ['default_payment_method' => $defaultPaymentMethod]
        );
    }

    /**
     * Sets Stripe default payment method
     *
     * @param Request $request
     */
    public function setDefaultPaymentMethod(Request $request) {

        if (Gate::denies('isAdmin')) {
            http_response_code(401);
            return 'Unauthorized';
        }

        $request->validate([
            'custId' => 'string',
            'paymentMethodId' => 'string|required',
        ]);

        if (empty($request->custId)) {
            $custId = Subscription::where(['client_id' => Auth::user()->client->id])->get()->first()->cust_id;
        } else {
            $custId = $request->custId;
        }

        $payment_method = $this->stripe->paymentMethods->retrieve($request->paymentMethodId);
        $payment_method->attach(['customer' => $custId]);

        // Set the default payment method on the customer
        $this->stripe->customers->update($custId, [
            'invoice_settings' => ['default_payment_method' => $request->paymentMethodId],
        ]);

        return $this->getPaymentMethods();
    }

    public function deletePaymentMethod(Request $request) {

        if (Gate::denies('isAdmin')) {
            http_response_code(401);
            return 'Unauthorized';
        }

        $this->stripe->paymentMethods->detach($request->id);

        $custId = Subscription::where(['client_id' => Auth::user()->client->id])->get()->first()->cust_id;
        $paymentMethods = $this->stripe->paymentMethods->all(['customer' => $custId, 'type' => 'card']);
        if ( ! empty($paymentMethods->count())) {
            $this->stripe->customers->update($custId, [
                'invoice_settings' => ['default_payment_method' => $paymentMethods->data[0]->id],
            ]);
        }

        return array_merge(
            $paymentMethods->toArray(),
            ['default_payment_method' => $paymentMethods->count() ? $paymentMethods->data[0]->id : null]
        );
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

        $request->validate(['custId' => 'string|required']);

        $sub = Subscription::where(['client_id' => Auth::user()->client->id, 'cust_id' => $request->custId])->first();

        if (empty($sub)) {
            http_response_code(404);
            return 'Subscription record not found.';
        }

        if (! empty($sub->subscription_id)) {

            $subscription = $this->stripe->subscriptions->retrieve($sub->subscription_id);
        } else {
            // Create the subscription
            $isDev = config('app.env') != 'production';
            $priceId = $isDev ? 'price_1HmJC7AdSpfz7pjgcTUb2tkl' : 'price_1J6DTtAdSpfz7pjgUAtQmS6R';
            $subscription = $this->stripe->subscriptions->create([
                'customer' => $request->custId,
                'items' => [['price' => $priceId]],
                'expand' => ['latest_invoice.payment_intent'],
            ]);
            $sub->subscription_id = $subscription->id;
            $sub->item_id = $subscription->items->data[0]->id;
            $sub->current_period_end = $subscription->current_period_end;
            $sub->price_id = $subscription->items->data[0]->price->id;
            $sub->status = $subscription->status;
            $sub->save();
            Mail::to(Auth::user())->send(new Welcome());
        }

        return $subscription;
    }

    /**
     * Stripe webhook handler
     *
     * @param Request $request
     */
    public function handle(Request $request, StripeClient $stripeClient) {
        $endpoint_secret = config('services.stripe.webhook_secret');

        $payload = @file_get_contents('php://input');
        $event = Webhook::constructEvent($payload, $_SERVER['HTTP_STRIPE_SIGNATURE'], $endpoint_secret); // Stripe Event object, see https://stripe.com/docs/api/events

        Log::info("Handling Stripe webhook for $event->type");

        $invoice = $event->data->object;

        $subscription = Subscription::where('subscription_id', $invoice->subscription)->first();
        $stripeSub = $stripeClient->subscriptions->retrieve($subscription->subscription_id);
        $subscription->status = $stripeSub->status;
        $subscription->current_period_end = $stripeSub->current_period_end;
        $subscription->save();

        $client = $subscription->client;
        $to = DB::table('users')
            ->join('user_role', 'users.id', '=', 'user_role.user_id')
            ->where(['users.client_id' => $subscription->client_id, 'user_role.role_id' => 2, 'active' => true])
            ->first();

        switch ($event->type) {
            case 'invoice.paid':
                Log::info("Payment succeeded for $client->name");
                Mail::to($to)->bcc(config('mail.from.address'))
                    ->send(new ProcessedPayment($invoice, $client) );
                break;
            case 'invoice.payment_failed':
                Log::info("Payment failed for $client->name");
                Mail::to($to)->bcc(config('mail.from.address'))
                    ->send(new ProcessedPayment($invoice, $client) );
                break;
            default:
                // Unhandled event type
                Log::info('Stripe Webhook ' . $request->type . $event->all()->toJSON());
        }
    }
}

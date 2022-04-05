<?php

namespace App\Http\Controllers;

use App\Mail\MemberCreated;
use App\Mail\MemberUpdated;
use App\Mail\PaymentProcessedStudent;
use App\Mail\PaymentUpcomingStudent;
use App\Models\Client;
use App\Models\Member;
use App\Models\Price;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\StripeClient;
use Stripe\Webhook;

class MemberController extends Controller
{
    /**
     * PaymentController constructor.
     * @param StripeClient $stripe
     */
    public function __construct(private StripeClient $stripe) {}

    /**
     * Finds or creates a Stripe customer for a given user
     *
     * @param Client $client
     * @param User $user
     *
     * @return array|JsonResponse
     */
    public function findOrCreateCustomer(Client $client, User $user) {

        if ((Gate::denies('isAdmin') && Gate::denies('isSuperAdmin')) || $user->client_id !== $client->id) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $member = $user->members->first();

        if ( ! empty($member) && ! empty($member->cust_id)) {

            $customer = $this->stripe->customers->retrieve(
                $member->cust_id,
                null,
                ["stripe_account" => $client->stripe_account]
            );
        } else {
            $customer = $this->stripe->customers->create([
                'name' => $user->name,
                'email' => $user->email,
                'metadata' => ['client_id' => $user->client->id, 'client_name' => $client->name],
            ], ["stripe_account" => $client->stripe_account]);;

            $member = new Member([
                'client_id' => $client->id,
                'user_id' => $user->id,
                'cust_id' => $customer->id,
            ]);
            $member->save();
        }

        return ['customer' => $customer, 'stripe_account_id' => $client->stripe_account];
    }

    /**
     * Creates membership subscription for a student
     *
     * @param Request $request
     * @param Client $client
     * @param User $user
     * @param Product $product
     * @param Price $price
     */
    public function create(Request $request, Client $client, User $user, Product $product, Price $price) {

        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || $user->client_id !== $client->id
            || $product->client_id !== $client->id
            || $price->client_id !== $client->id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate(['trial_period_days' => 'integer|required']);

        $customer = $this->findOrCreateCustomer($client, $user)['customer'];

        $member = Member
            ::where(['client_id' => $client->id, 'user_id' => $user->id, 'cust_id' => $customer->id])
            ->whereNull('subscription_id')
            ->first();

        if ($member === null) {
            $member = new Member([
                'client_id' => $client->id,
                'user_id' => $user->id,
                'cust_id' => $customer->id,
            ]);
        }

        $subscription = $this->stripe->subscriptions->create([
            'customer' => $customer->id,
            'items' => [['price' => $price->id]],
            'expand' => ['latest_invoice.payment_intent'],
            'application_fee_percent' => 2,
            'trial_period_days' => $request->input('trial_period_days'),
        ], ["stripe_account" => $client->stripe_account]);

        $member->subscription_id = $subscription->id;
        $member->item_id = $subscription->items->data[0]->id;
        $member->current_period_end = $subscription->current_period_end;
        $member->price_id = $subscription->items->data[0]->price->id;
        $member->status = $subscription->status;
        $member->save();

        $isDev = config('app.env') != 'production';
        $to = $isDev ? config('mail.from.address') : $user->email;
        Log::info(__('log.member_created', [
            'client' => $member->client->name,
            'member' => $member->user->name,
            'product' => $product->name,
            'amount' => $price->amount,
            'period_count' => $price->period_count,
            'period' => $price->period,
        ]));
        Mail::to($to)->bcc(config('mail.from.address'))->send(new MemberCreated($client, $product));

        return $subscription;
    }

    /**
     * Gets all membership subscriptions for a given client
     *
     * @param Client $client
     */
    public function read(Client $client)
    {
        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin')) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        return Member::where('client_id', $client->id)->with(['user', 'price.product'])->get();
    }

    /**
     * Updates price of membership subscription for a student
     *
     * @param Member $member
     * @param Product $product
     * @param Price $price
     *
     * @return JsonResponse|\Stripe\Subscription
     */
    public function update(Member $member, Product $product, Price $price) {

        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || Auth::user()->client_id !== $member->client_id
            || Auth::user()->client_id !== $product->client_id
            || $price->product_id !== $product->id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        if (empty($member->cust_id) || empty($member->subscription_id)) {

            return response()->json(['error' => 'Member subscription not found.'], 404);
        }

        $subscription = $this->stripe->subscriptions->update(
            $member->subscription_id,
            ['items' => [['id' => $member->item_id, 'price' => $price->id]], 'expand' => ['latest_invoice.payment_intent']],
            ["stripe_account" => $member->client->stripe_account]
        );

        $member->subscription_id = $subscription->id;
        $member->item_id = $subscription->items->data[0]->id;
        $member->current_period_end = $subscription->current_period_end;
        $member->price_id = $subscription->items->data[0]->price->id;
        $member->status = $subscription->status;
        $member->save();

        $isDev = config('app.env') != 'production';
        $to = $isDev ? config('mail.from.address') : $member->user->email;
        $logMessage = __('log.member_updated', [
            'client' => $member->client->name,
            'member' => $member->user->name,
            'amount' => $price->amount,
            'period_count' => $price->period_count,
            'period' => $price->period,
            'product' => $product->name,
        ]);
        Log::info($logMessage);
        Mail::to($to)->bcc(config('mail.from.address'))->send(new MemberUpdated($logMessage, $product));

        return $subscription;
    }

    /**
     * Stripe webhook handler
     *
     * @param Request $request
     * @param StripeClient $stripeClient
     */
    public function handle() {
        $endpoint_secret = config('services.stripe.connect_webhook_secret');

        $payload = @file_get_contents('php://input');

        try {
            $event = Webhook::constructEvent($payload, $_SERVER['HTTP_STRIPE_SIGNATURE'], $endpoint_secret); // Stripe Event object, see https://stripe.com/docs/api/events
        } catch(\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        Log::info("Handling Stripe Connect webhook for $event->type");

        if ($event->data->object->object == 'subscription') {

            if ($event->type == 'customer.subscription.deleted' || $event->type == 'customer.subscription.updated') {

                $subscription = $event->data->object;
                $member = Member::where('subscription_id', $subscription->id)->first();
                $member->current_period_end = $subscription->current_period_end;
                $member->status = $subscription->status;
                $member->price_id = $subscription->items->data[0]->price->id;
                $member->pause_collection = ! empty($subscription->pause_collection);
                $member->resumes_at = $member->pause_collection ? $subscription->pause_collection->resumes_at : null;
                $member->cancel_at_period_end = $subscription->cancel_at_period_end;
                $member->save();
            }
         }

        if ($event->data->object->object == 'invoice') {

            $invoice = $event->data->object;

            $member = Member::where('subscription_id', $invoice->subscription)->first();
            $stripeSub = $this->stripe->subscriptions->retrieve(
                $member->subscription_id,
                [],
                ["stripe_account" => $member->client->stripe_account]
            );
            $member->status = $stripeSub->status;
            $member->current_period_end = $stripeSub->current_period_end;
            $member->save();

            $student = $member->user;
            $isDev = config('app.env') != 'production';
            $to = $isDev ? config('mail.from.address') : $student;

            switch ($event->type) {
                case 'invoice.upcoming':
                    Log::info("{$student->client->name} - Payment upcoming for $student->name");
                    $message = Mail::to($to);
                    if ( ! $isDev) {
                        $message->bcc($student->client->first_admin);
                    }
                    $message->send(new PaymentUpcomingStudent($invoice, $student));
                    break;
                case 'invoice.paid':
                    Log::info("{$student->client->name} - Payment succeeded for $student->name");
                    $message = Mail::to($to);
                    if ( ! $isDev) {
                        $message->bcc($student->client->first_admin);
                    }
                    $message->send(new PaymentProcessedStudent($invoice, $student));
                    break;
                case 'invoice.payment_failed':
                    Log::error("{$student->client->name} - Payment failed for $student->name");
                    Mail::send('emails.payment_failed_student', [], function($message) use ($to, $isDev, $student, $invoice) {
                        $message->from($student->client->firstAdmin);
                        $message->to($to);
                        if ( ! $isDev) {
                            $message->bcc($student->client->first_admin);
                        }
                        $message->subject('Payment to '. $student->client->name . ' failed');
                    });
            }
        }
    }

    /**
     * Pauses membership subscription for a student
     *
     * @param Member $member
     * @param Product $product
     *
     * @return JsonResponse|\Stripe\Subscription
     */
    public function pause(Request $request, Member $member, Product $product) {

        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || Auth::user()->client_id !== $member->client_id
            || Auth::user()->client_id !== $product->client_id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        if (empty($member->cust_id) || empty($member->subscription_id)) {
            return response()->json(['error' => 'Member subscription not found.'], 404);
        }

        $request->validate(['resume_collection' => 'nullable|date']);

        $params = ['pause_collection' => ['behavior' => 'void']];
        if ( ! empty($request->resume_collection)) {

            $resumeAt = Carbon::parse($request->resume_collection);
            $params['pause_collection']['resumes_at'] = $resumeAt->isoFormat('X');
        }
        $subscription = $this->stripe->subscriptions->update(
            $member->subscription_id,
            $params,
            ["stripe_account" => $member->client->stripe_account]
        );

        $member->subscription_id = $subscription->id;
        $member->item_id = $subscription->items->data[0]->id;
        $member->current_period_end = $subscription->current_period_end;
        $member->price_id = $subscription->items->data[0]->price->id;
        $member->status = $subscription->status;
        $member->pause_collection = ! empty($subscription->pause_collection);
        $member->resumes_at = $subscription->pause_collection->resumes_at;
        $member->save();

        $isDev = config('app.env') != 'production';
        $to = $isDev ? config('mail.from.address') : $member->user->email;
        $logMessage = __('log.member_paused', ['client' => $member->client->name, 'member' => $member->user->name]);
        Log::info($logMessage);
        Mail::to($to)->bcc(config('mail.from.address'))->send(new MemberUpdated($logMessage, $product));

        return $subscription;
    }

    /**
     * Resumes membership subscription for a student
     *
     * @param Member $member
     * @param Product $product
     *
     * @return JsonResponse|\Stripe\Subscription
     */
    public function resume(Member $member, Product $product) {

        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || Auth::user()->client_id !== $member->client_id
            || Auth::user()->client_id !== $product->client_id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        if (empty($member->cust_id) || empty($member->subscription_id)) {
            return response()->json(['error' => 'Member subscription not found.'], 404);
        }

        $subscription = $this->stripe->subscriptions->update(
            $member->subscription_id,
            ['pause_collection' => '', 'cancel_at_period_end' => false],
            ["stripe_account" => $member->client->stripe_account]
        );

        $member->subscription_id = $subscription->id;
        $member->item_id = $subscription->items->data[0]->id;
        $member->current_period_end = $subscription->current_period_end;
        $member->cancel_at_period_end = $subscription->cancel_at_period_end;
        $member->price_id = $subscription->items->data[0]->price->id;
        $member->status = $subscription->status;
        $member->pause_collection = ! empty($subscription->pause_collection);
        $member->save();

        $isDev = config('app.env') != 'production';
        $to = $isDev ? config('mail.from.address') : $member->user->email;
        $logMessage = __('log.member_resumed', ['client' => $member->client->name, 'member' => $member->user->name]);
        Log::info($logMessage);
        Mail::to($to)->bcc(config('mail.from.address'))->send(new MemberUpdated($logMessage, $product));

        return $subscription;
    }

    /**
     * Cancels membership subscription for a student
     *
     * @param Request $request
     * @param Member $member
     * @param Product $product
     *
     * @return JsonResponse|\Stripe\Subscription
     */
    public function cancel(Request $request, Member $member, Product $product) {

        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || Auth::user()->client_id !== $member->client_id
            || Auth::user()->client_id !== $product->client_id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        if (empty($member->cust_id) || empty($member->subscription_id)) {
            return response()->json(['error' => 'Member subscription not found.'], 404);
        }

        if ($request->cancel_at_period_end) {

            $subscription = $this->stripe->subscriptions->update(
                $member->subscription_id,
                ['cancel_at_period_end' => true],
                ["stripe_account" => $member->client->stripe_account]
            );
            $effective = new Carbon($subscription->current_period_end);
            $effective = $effective->format('Y-m-d');
        } else {
            $subscription = $this->stripe->subscriptions->cancel(
                $member->subscription_id,
                null,
                ["stripe_account" => $member->client->stripe_account]
            );
            $effective = 'immediately';
        }


        $member->subscription_id = $subscription->id;
        $member->item_id = $subscription->items->data[0]->id;
        $member->current_period_end = $subscription->current_period_end;
        $member->price_id = $subscription->items->data[0]->price->id;
        $member->status = $subscription->status;
        $member->cancel_at_period_end = $subscription->cancel_at_period_end;
        $member->save();

        $isDev = config('app.env') != 'production';
        $to = $isDev ? config('mail.from.address') : $member->user->email;
        $logMessage = __('log.member_deleted', [
            'client' => $member->client->name,
            'member' => $member->user->name,
            'effective' => $effective,
        ]);
        Log::info($logMessage);
        Mail::to($to)->bcc(config('mail.from.address'))->send(new MemberUpdated($logMessage, $product));

        return $subscription;
    }
}

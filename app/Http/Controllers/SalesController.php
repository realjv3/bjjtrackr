<?php

namespace App\Http\Controllers;

use App\Facades\PaymentMethods;
use App\Models\Client;
use App\Models\Member;
use App\Models\Price;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Stripe\Exception\InvalidRequestException;
use Stripe\StripeClient;

class SalesController extends Controller
{
    /**
     * PaymentController constructor.
     * @param StripeClient $stripe
     */
    public function __construct(private StripeClient $stripe) {}

    /**
     * Creates a Stripe payment intent
     *
     * @param Client $client
     * @param Price $price
     * @param string|null $cust_id - Stripe customer ID
     *
     * @return array|JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function createPaymentIntent(Client $client, Price $price, string $cust_id = null) {

        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || $price->client_id !== $client->id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $params = [
            'amount' => (float) substr($price->amount, 1) * 100,
            'currency' => 'usd',
            'metadata' => ['product_id' => $price->product->id, 'product' => $price->product->name],
            'automatic_payment_methods' => ['enabled' => true],
        ];
        if ($cust_id !== null) {

            $member = Member::where('cust_id', $cust_id)->first();

            $params['customer'] = $cust_id;
            $params['metadata']['cust_name'] = $member->user->name;
            $params['payment_method'] = PaymentMethods::getAllPaymentMethods($member->client, $member->user)[1];
        }

        try {
            $intent =  $this->stripe->paymentIntents->create($params, ["stripe_account" => $client->stripe_account]);

            return ['id' => $intent->id, 'clientSecret' => $intent->client_secret];

        } catch (InvalidRequestException $exception) {

            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Cancels a Stripe payment intent
     *
     * @param Client $client
     *
     * @return array|JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function cancelPaymentIntent(Request $request, Client $client) {

        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin')) {

            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate(['paymentIntentId' => 'string|required']);

        try {
            $intent =  $this->stripe->paymentIntents->cancel(
                $request->paymentIntentId,
                ["cancellation_reason" => 'abandoned'],
                ["stripe_account" => $client->stripe_account]
            );

            return ['success' => true, 'clientSecret' => $intent->client_secret];

        } catch (InvalidRequestException $exception) {

            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    /**
     * Persists a sale
     *
     * @param Request $request
     * @param Client $client
     *
     * @return Sale|JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function create(Request $request, Client $client) {

        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin')) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'paymentIntent' => 'required',
            'sale' => 'required',
        ]);

        $sale = new Sale();
        $sale->id = $request->paymentIntent['id'];
        $sale->client()->associate($client->id);
        $sale->product()->associate($request->sale['product_id']);
        $sale->price()->associate($request->sale['price_id']);
        $sale->status = $request->paymentIntent['status'];

        if (isset($request->sale['user_id'])) {

            $sale->user()->associate($request->sale['user_id']);
        }

        $paymentMethod = $this->stripe->paymentMethods->retrieve(
            $request->paymentIntent['payment_method'],
            null,
            ['stripe_account' => $client->stripe_account]
        );

        $sale->payment_method = ucfirst($paymentMethod->card->brand)
            . ' ..'
            . $paymentMethod->card->last4
            . ' exp. '
            . $paymentMethod->card->exp_month
            . '/'
            . substr($paymentMethod->card->exp_year, 2);

        $sale->save();

        return $sale;
    }

    /**
     * Gets all sales for a given client
     *
     * @param Client $client
     *`
     * @return array|JsonResponse
     */
    public function read(Client $client)
    {
        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin')) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        return Sale::where('client_id', $client->id)->with(['user', 'product', 'price'])->orderByDesc('created_at')->get();
    }

    /**`
     * Refunds a given payment intent
     *
     * @param Request $request
     * @param Client $client
     * @param Sale $sale
     *`
     * @return array|JsonResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function refund(Request $request, Client $client, Sale $sale)
    {
        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || $sale->client_id !== $client->id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'amount' => 'required|numeric',
        ]);

        if ((float) $request->amount <= 0) {
            return response()->json(['error' => 'Refund amount must be greater than zero.'], 400);
        }

        try {
            // create the refund
            $this->stripe->refunds->create(
                ['payment_intent' => $sale->id],
                ["stripe_account" => $client->stripe_account]
            );

            // update the sale in DB
            $sale->status = 'refunded';
            $sale->save();

            return ['success' => true, 'sale' => $sale];

        } catch (InvalidRequestException $exception) {

            return response()->json(['error' => $exception->getMessage()], 400);
        }
    }
}

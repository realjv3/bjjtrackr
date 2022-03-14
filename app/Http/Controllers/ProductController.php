<?php

namespace App\Http\Controllers;

use App\Enums\PricePeriod;
use App\Models\Client;
use App\Models\Price;
use App\Models\Product;
use App\Rules\PricePeriodCount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rules\Enum;
use Stripe\Exception\ApiErrorException;
use Stripe\StripeClient;

class ProductController extends Controller
{
    /**
     * PaymentController constructor.
     *
     * @param StripeClient $stripe
     */
    public function __construct(private StripeClient $stripe) {}

    /**
     * Creates new product for a given client
     *
     * @param Request $request
     * @param Client $client
     */
    public function create(Request $request, Client $client) {

        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin') ) {

            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'name' => 'string|required',
            'unit' => 'string',
            'prices' => 'array|required|min:1',
            'prices.*.amount' => 'numeric|required',
            'prices.*.recurring' => 'boolean',
            'prices.*.period' => [new Enum(PricePeriod::class), 'required_if:prices.*.recurring,true'],
            'prices.*.period_count' => ['integer', 'required_if:prices.*.recurring,true', new PricePeriodCount()],
        ]);

        try {
            $productResponse = $this->stripe->products->create([
                'name' => $request->input('name'),
                'unit_label' => $request->input('unit'),
            ], ["stripe_account" => $client->stripe_account]);

            $product = new Product(['name' => $request->input('name'), 'unit' => $request->input('unit')]);
            $product->id = $productResponse->id;
            $product->client_id = $client->id;
            $product->save();

            foreach ($request->input('prices') as $newPrice) {
                $priceData = [
                    'currency' => 'usd',
                    'product' => $product->id,
                    'unit_amount' => $newPrice['amount'] * 100,
                ];
                $isRecurring = $newPrice['recurring'] == true;
                if ($isRecurring) {
                    $priceData['recurring'] = [
                        'interval' => $newPrice['period'],
                        'interval_count' => $newPrice['period_count'],
                    ];
                }
                $priceResponse = $this->stripe->prices->create($priceData, ["stripe_account" => $client->stripe_account]);

                $price = new Price(['amount' => $priceResponse->unit_amount]);
                $price->id = $priceResponse->id;
                $price->client_id = $client->id;
                $price->product_id = $product->id;
                if ($isRecurring) {
                    $price->recurring = 1;
                    $price->period = $priceResponse->recurring->interval;
                    $price->period_count = $priceResponse->recurring->interval_count;
                }
                $price->save();
            }

            return $product;

        } catch (ApiErrorException $exception) {

            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }

    /**
     * Gets single or all products for a given client
     *
     * @param Client $client
     * @param Product|null $product
     */
    public function read(Client $client) {

        if (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin') ) {

            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $this->syncProducts($client);

        return Product::where('client_id', $client->id)->get();
    }

    /**
     * Syncs a given client's Stripe products and prices
     * @param Client $client
     *
     * @return void
     */
    private function syncProducts(Client $client)
    {
        // Sync products with client's Stripe account
        $products = $this->stripe->products->all(null, ["stripe_account" => $client->stripe_account]);
        $deletedProducts = Product::where('client_id', $client->id)->get();

        // sync product models with Stripe products
        foreach ($products as $product) {

            $productModel = Product::find($product->id);
            // create product model that is present in Stripe but not Flowrolled
            if ($productModel === null) {
                $productModel = new Product([
                    'name' => $product->name,
                    'unit' => $product->unit_label,
                    'active' => $product->active,
                ]);
                $productModel->id = $product->id;
                $productModel->client_id = $client->id;
                $productModel->save();
            // update product model that is present in Stripe and Flowrolled
            } else {
                $deletedProducts = $deletedProducts->except([$productModel->id]); // reduce product models collection until only deleted products remain
                $productModel->fill([
                    'name' => $product->name,
                    'unit' => $product->unit_label,
                    'active' => $product->active,
                ]);
                $productModel->save();
            }
        }

        // delete products & prices (via foreign key cascade) that are no longer present on Stripe
        foreach ($deletedProducts as $deletedProduct) {
            $deletedProduct->delete();
        }

        // Sync prices with client's Stripe account
        $prices = $this->stripe->prices->all(null, ["stripe_account" => $client->stripe_account]);
        $deletedPrices = Price::where('client_id', $client->id)->get();

        foreach ($prices as $price) {

            $priceModel = Price::find($price->id);
            $isRecurring = ! empty($price->recurring);

            // create price model that is present in Stripe but not Flowrolled
            if ($priceModel === null) {
                $priceModel = new Price([
                    'amount' => $price->unit_amount,
                    'recurring' => $isRecurring ? true : false,
                    'active' => $price->active,
                ]);
                if ($isRecurring) {
                    $priceModel->period = $price->recurring->interval;
                    $priceModel->period_count = $price->recurring->interval_count;
                }
                $priceModel->id = $price->id;
                $priceModel->client_id = $client->id;
                $priceModel->product_id = $price->product;
                $priceModel->save();
            // update price model that is present in Stripe and Flowrolled
            } else {
                $deletedPrices = $deletedPrices->except([$priceModel->id]); // reduce price models collection until only deleted prices remain
                $priceModel->fill([
                    'amount' => $price->unit_amount,
                    'recurring' => $isRecurring ? true : false,
                    'active' => $price->active,
                ]);
                if ($isRecurring) {
                    $priceModel->period = $price->recurring->interval;
                    $priceModel->period_count = $price->recurring->interval_count;
                }
                $priceModel->save();
            }
        }

        // delete prices that are no longer present on Stripe
        foreach ($deletedPrices as $deletedPrice) {
            $deletedPrice->delete();
        }
    }

    /**
     * Updates existing product for a given client
     *
     * @param Request $request
     * @param Client $client
     * @param Product $product
     */
    public function update(Request $request, Client $client, Product $product) {

        if (
            (Gate::denies('isAdmin') && Gate::denies('isSuperAdmin'))
            || $product->client_id !== $client->id
        ) {
            return response()->json(['error' => 'Unauthorized.'], 401);
        }

        $request->validate([
            'id' => 'string|required',
            'name' => 'string|required',
            'unit' => 'string',
            'active' => 'boolean',
            'prices' => 'array|required|min:1',
            'prices.*.id' => 'string',
            'prices.*.amount' => 'numeric|required',
            'prices.*.product_id' => 'string',
            'prices.*.recurring' => 'boolean',
            'prices.*.period' => [new Enum(PricePeriod::class), 'required_if:prices.*.recurring,true'],
            'prices.*.period_count' => ['integer', 'required_if:prices.*.recurring,true', new PricePeriodCount()],
            'prices.*.active' => 'boolean',
        ]);

        try {
            $productResponse = $this->stripe->products->update($product->id, [
                'name' => $request->input('name'),
                'unit_label' => $request->input('unit'),
                'active' => (int) $request->input('active') ? true : false,
            ], ["stripe_account" => $client->stripe_account]);

            $product->name = $productResponse->name;
            $product->unit = $productResponse->unit_label;
            $product->save();

            foreach ($request->input('prices') as $editPrice) {

                $isRecurring = $editPrice['recurring'] == true;

                if (empty($editPrice['id'])) {

                    $priceData = [
                        'currency' => 'usd',
                        'product' => $product->id,
                        'unit_amount' => $editPrice['amount'] * 100,
                        'active' => $editPrice['active'] ?? true,
                    ];
                    if ($isRecurring) {
                        $priceData['recurring'] = [
                            'interval' => $editPrice['period'],
                            'interval_count' => $editPrice['period_count'],
                        ];
                    }

                    $priceResponse = $this->stripe->prices->create($priceData, ["stripe_account" => $client->stripe_account]);
                    $price = new Price();
                    $price->id = $priceResponse->id;
                    $price->client_id = $client->id;
                    $price->product_id = $product->id;
                } else {
                    $priceResponse = $this->stripe->prices->update(
                        $editPrice['id'],
                        ['active' => (int) $editPrice['active'] ? true : false],
                        ["stripe_account" => $client->stripe_account]
                    );
                    $price = Price::find($editPrice['id']);
                }

                $price->amount = $priceResponse->unit_amount;
                if ($isRecurring) {
                    $price->recurring = 1;
                    $price->period = $priceResponse->recurring->interval;
                    $price->period_count = $priceResponse->recurring->interval_count;
                }
                $price->save();
            }


            return $product;

        } catch (ApiErrorException $exception) {

            return response()->json(['error' => $exception->getMessage()], 500);
        }
    }
}

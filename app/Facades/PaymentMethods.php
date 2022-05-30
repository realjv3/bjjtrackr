<?php

namespace App\Facades;

use App\Modules\PaymentMethods as PaymentMethodsModule;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array getAllPaymentMethods(App\Models\Client $client, App\Models\User $user)
 * @method static void setDefault(App\Models\Client $client, string $paymentMethodId, string $custId)
 * @method static void detach(App\Models\Client $client, string $paymentMethodId)
 */
class PaymentMethods extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     *
     * @throws \RuntimeException
     */
    protected static function getFacadeAccessor()
    {
        return PaymentMethodsModule::class;
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Stripe\StripeClient;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(StripeClient $stripe)
    {
        $this->registerPolicies();

        Gate::define('isSuperAdmin', function ($user) {

            $isSuperAdmin = false;
            $roles = $user->roles;

            foreach ($roles as $role) {
                if ($role->role == 'Super Admin') {
                    $isSuperAdmin = true;
                    break;
                }
            }
            return $isSuperAdmin;
        });

        Gate::define('isAdmin', function ($user) {

            $isAdmin = false;
            $roles = $user->roles;

            foreach ($roles as $role) {
                if ($role->role == 'Administrator') {
                    $isAdmin = true;
                    break;
                }
            }
            return $isAdmin;
        });

        Gate::define('isInstructor', function ($user) {

            $isInstructor = false;
            $roles = $user->roles;

            foreach ($roles as $role) {
                if ($role->role == 'Instructor') {
                    $isInstructor = true;
                    break;
                }
            }
            return $isInstructor;
        });

        Gate::define('isStudentOnly', function ($user) {

            return Gate::denies('isSuperAdmin') && Gate::denies('isAdmin') && Gate::denies('isInstructor');
        });

        Gate::define('subscriptionActive', function ($user) use ($stripe) {

            $client = $user->client;

            if (empty($client->subscription) || ! in_array($client->subscription->status, ['active', 'trialing'])) {
                return false;
            }

            $paymentMethods = $stripe->paymentMethods->all(['customer' => $client->subscription->cust_id, 'type' => 'card']);

            if ($paymentMethods->isEmpty()) {
                return false;
            }

            return true;
        });
    }
}

<?php

namespace App\Console\Commands;

use App\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use Stripe\SubscriptionItem;

class ReportUsage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'report:usage';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reports number of students to Stripe for subscription calculation';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Beginning usage report');
        Log::info('Beginning usage report');
        $clients = Client::all();

        foreach ($clients as $client) {
            $studentCount = DB::table('users')
                ->join('user_role', 'users.id', '=', 'user_role.user_id')
                ->where(['users.client_id' => $client->id, 'user_role.role_id' => 4, 'active' => true])
                ->count();

            $date = date_create();
            $timestamp = date_timestamp_get($date);
            // The idempotency key allows you to retry this usage record call if it fails.
            $idempotency_key = Str::uuid();
            Stripe::setApiKey(config('services.stripe.secret_key'));

            try {
                SubscriptionItem::createUsageRecord(
                    $client->subscription->item_id,
                    ['quantity' => $studentCount, 'timestamp' => $timestamp, 'action' => 'set'],
                    ['idempotency_key' => $idempotency_key]
                );
                $this->info("$client->name is using $studentCount students.");
                Log::info("$client->name is using $studentCount students.");
            } catch (ApiErrorException $e) {
                $error = $e->getMessage();
                $this->error("Usage report failed for item ID $client->name with idempotency key $idempotency_key: $error");
                Log::error("Usage report failed for item ID $client->name with idempotency key $idempotency_key: $error");
            }
        }
        return 0;
    }
}

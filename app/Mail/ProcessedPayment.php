<?php

namespace App\Mail;

use App\Client;
use App\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Stripe\StripeClient;

class ProcessedPayment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var Subscription
     */
    protected $subscription;

    /**
     * @var \stdClass
     */
    protected $paymentIntent;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Create a new message instance.
     *
     * @param $paymentIntent
     * @param $client
     */
    public function __construct(Subscription $subscription, $paymentIntent, $client)
    {
        $this->subscription = $subscription;
        $this->paymentIntent = $paymentIntent;
        $this->client = $client;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(StripeClient $stripeClient)
    {
        $clientName = $this->client->name;
        $amount = $this->paymentIntent->amount;
        $status = $this->paymentIntent->status;
        $charge = $this->paymentIntent->charges->data;
        $usage = $stripeClient->subscriptionItems->allUsageRecordSummaries($this->subscription->item_id)->data;

        if ($status == 'succeeded') {

            $content = "Payment of $amount for total usage of $usage->total_usage succeeded for $clientName.";
        } else {
            $content = "Payment of $amount for total usage of $usage->total_usage failed for $clientName.
             $charge->failure_message.";
        }

        return $this->view('emails.payment')->with('content', $content);
    }
}

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
    protected $invoice;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Create a new message instance.
     *
     * @param $invoice
     * @param $client
     */
    public function __construct(Subscription $subscription, $invoice, $client)
    {
        $this->subscription = $subscription;
        $this->invoice = $invoice;
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
        $amount = $this->invoice->amount_due;
        $status = $this->invoice->status;
        $usage = $stripeClient->subscriptionItems->allUsageRecordSummaries($this->subscription->item_id)->data[0]['total_usage'];

        if ($status == 'paid') {

            $content = "Payment of $amount for total usage of $usage succeeded for $clientName.";
        } else {
            $content = "Payment of $amount for total usage of $usage failed for $clientName.";
        }

        return $this->view('emails.payment')->with('content', $content);
    }
}

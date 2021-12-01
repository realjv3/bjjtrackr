<?php

namespace App\Mail;

use App\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Stripe\Invoice;
use Stripe\StripeClient;

class ProcessedPayment extends Mailable
{
    use Queueable, SerializesModels;

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
    public function __construct(Invoice $invoice, Client $client)
    {
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
        $status = $this->invoice->status;

        if ($status == 'paid') {

            return $this->view('emails.payment')->with([
                'client' => $this->client,
                'invoice' => $this->invoice,
            ]);
        }
    }
}

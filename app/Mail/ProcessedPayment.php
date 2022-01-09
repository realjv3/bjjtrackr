<?php

namespace App\Mail;

use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Stripe\Invoice;

class ProcessedPayment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Invoice $invoice
     * @param Client $client
     */
    public function __construct(protected Invoice $invoice, protected Client $client)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
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

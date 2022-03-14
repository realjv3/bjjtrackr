<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberCreated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Client $client
     * @param Product $product
     */
    public function __construct(protected Client $client, protected Product $product)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->client->firstAdmin)
            ->subject('New membership')
            ->view('emails.membercreated')
            ->with([
                'client' => $this->client,
                'product' => $this->product,
            ]);
    }
}

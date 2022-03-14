<?php

namespace App\Mail;

use App\Models\Product;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MemberUpdated extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param string $message
     * @param Product $product
     */
    public function __construct(protected string $message, protected Product $product)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->product->client->firstAdmin)
            ->subject('Changed membership')
            ->view('emails.memberupdated')
            ->with([
                'client' => $this->product->client,
                'msg' => $this->message,
                'product' => $this->product,
            ]);
    }
}

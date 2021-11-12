<?php

namespace App\Mail;

use App\Client;
use App\Document;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplateUploaded extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Feedback constructor.
     * @param string $content
     */
    public function __construct(protected Client $client, protected User $user, protected Document $document)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject('A template was uploaded to FlowRolled for ' . $this->client->name)
            ->view('emails.template_uploaded')
            ->with(['client' => $this->client, 'user' => $this->user, 'document' => $this->document]);
    }
}

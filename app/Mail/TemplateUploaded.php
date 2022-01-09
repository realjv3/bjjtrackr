<?php

namespace App\Mail;

use App\Models\Client;
use App\Models\Document;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TemplateUploaded extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Feedback constructor.
     *
     * @param Client $client
     * @param User $user
     *
     * @param Document $document
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

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class Feedback extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var $content string
     */
    protected $content;

    /**
     * Feedback constructor.
     * @param string $content
     */
    public function __construct(string $content)
    {
        $this->content = $content;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = Auth::user()->name;
        $email = Auth::user()->email;

        return $this->from($email, $name)
            ->subject('FlowRolled Feedback')
            ->view('emails.feedback')
            ->with('content', $this->content);
    }
}

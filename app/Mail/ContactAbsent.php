<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactAbsent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var $owner User
     */
    protected $owner;

    /**
     * @var $student User
     */
    protected $student;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(array $owner, User $student)
    {
        $this->owner = $owner;
        $this->student = $student;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->owner['email'], $this->owner['name'])
            ->subject('We miss you '. $this->student->name)
            ->view('emails.absent')
            ->with([
                'to' => $this->student->name,
                'from' => $this->owner['cname'],
            ]);
    }
}

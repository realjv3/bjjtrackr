<?php

namespace App\Mail;

use App\Belt;
use App\Rank;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EligibleForPromo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var $name string
     */
    protected $name;

    /**
     * @var $rank Rank
     */
    protected $rank;

    /**
     * Feedback constructor.
     * @param string $name
     * @param string $rank
     */
    public function __construct(string $name, Rank $rank)
    {
        $this->name = $name;
        $this->rank = $rank;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        if ($this->rank->stripes < 4) {
            $promotion = 'next stripe';
        } else {
            $nextBelt = Belt::find($this->rank->belt_id + 1);
            $promotion = lcfirst($nextBelt->belt) . ' belt';
        }
        return $this->from(config('mail.from.address'), $this->name)
            ->subject($this->name . ' is eligible for a promotion!')
            ->view('emails.eligible')
            ->with(['name' => $this->name, 'rank' => $promotion]);
    }
}

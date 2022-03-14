<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Stripe\Invoice;

class PaymentUpcomingStudent extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param Invoice $invoice
     * @param User $student
     */
    public function __construct(protected Invoice $invoice, protected User $student)
    {
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.payment_upcoming_student')
            ->from($this->student->client->firstAdmin)
            ->subject('A payment to '. $this->student->client->name . ' is coming up')
            ->with([
                'student' => $this->student,
                'invoice' => $this->invoice,
            ]);
    }
}

<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Stripe\Invoice;

class PaymentProcessedStudent extends Mailable
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
        if ($this->invoice->status == 'paid') {

            return $this->view('emails.payment_student')
                ->from($this->student->client->firstAdmin)
                ->subject('Thank you for your payment')
                ->with([
                    'student' => $this->student,
                    'invoice' => $this->invoice,
                ]);
        }
    }
}

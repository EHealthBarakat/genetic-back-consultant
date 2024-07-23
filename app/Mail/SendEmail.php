<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $view;
    public $values;

    public function __construct(array $values, string $subject, string $view)
    {
        $this->subject = $subject;
        $this->view = $view;
        $this->values=$values;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject($this->subject)
            ->view('emails.' . $this->view)
            ->with($this->values);
    }

}

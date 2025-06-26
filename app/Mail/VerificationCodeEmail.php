<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerificationCodeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public string $code)
    {
    }

    public function build()
    {
        return $this->subject('Code de vérification')
            ->view('emails.verification')
            ->with(['code' => $this->code]);
    }
}

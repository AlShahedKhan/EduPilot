<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $userName,
        public string $resetUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Password Reset Request',
            from: [get_mail_config('MAIL_FROM_ADDRESS'), get_mail_config('MAIL_FROM_NAME', 'School Management')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.passwordReset',
        );
    }
}

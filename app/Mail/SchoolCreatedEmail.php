<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolCreatedEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $schoolName,
        public string $adminName,
        public string $adminEmail,
        public string $adminPassword,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Welcome to {$this->schoolName} — Your Admin Account is Ready",
            from: [get_mail_config('MAIL_FROM_ADDRESS'), get_mail_config('MAIL_FROM_NAME', 'School Management')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.schoolCreated',
        );
    }
}

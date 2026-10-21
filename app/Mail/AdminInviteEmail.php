<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminInviteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $adminName,
        public string $schoolName,
        public string $loginUrl,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "You've been granted access to {$this->schoolName}",
            from: [get_mail_config('MAIL_FROM_ADDRESS'), get_mail_config('MAIL_FROM_NAME', 'School Management')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.adminInvite',
        );
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SchoolStatusEmail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $adminName,
        public string $schoolName,
        public string $status,
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->status === 'enable' ? 'Activated' : 'Deactivated';
        return new Envelope(
            subject: "{$this->schoolName} — School Account {$label}",
            from: [get_mail_config('MAIL_FROM_ADDRESS'), get_mail_config('MAIL_FROM_NAME', 'School Management')],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'email.schoolStatus',
        );
    }
}

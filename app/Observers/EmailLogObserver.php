<?php

namespace App\Observers;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;

class EmailLogObserver
{
    public function sending(MessageSending $event): void
    {
        try {
            $message = $event->message;
            $to = collect($message->getTo())->keys()->first();
            $from = collect($message->getFrom())->keys()->first() ?? get_mail_config('MAIL_FROM_ADDRESS');

            // Check if this message was already logged (by Sent event)
            $exists = EmailLog::where('to', $to)
                ->where('subject', $message->getSubject())
                ->where('created_at', '>', now()->subSeconds(5))
                ->exists();

            if ($exists) {
                return;
            }

            EmailLog::create([
                'from'           => $from,
                'to'             => $to,
                'subject'        => $message->getSubject(),
                'body'           => $this->stripTags($message->getBody()),
                'status'         => 'pending',
                'mailable_class' => $this->detectMailable($event->data),
                'tenant_id'      => $this->detectTenant(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('EmailLogObserver::sending failed', ['error' => $e->getMessage()]);
        }
    }

    public function sent(MessageSent $event): void
    {
        try {
            $message = $event->message;
            $to = collect($message->getTo())->keys()->first();

            $log = EmailLog::where('to', $to)
                ->where('subject', $message->getSubject())
                ->where('status', 'pending')
                ->latest()
                ->first();

            if ($log) {
                $log->update(['status' => 'sent']);
            }
        } catch (\Throwable $e) {
            Log::warning('EmailLogObserver::sent failed', ['error' => $e->getMessage()]);
        }
    }

    private function stripTags(string $html): string
    {
        return \Illuminate\Support\Str::limit(strip_tags($html), 2000);
    }

    private function detectMailable(array $data): ?string
    {
        foreach ($data as $value) {
            if (is_object($value) && str_starts_with(get_class($value), 'App\\Mail\\')) {
                return get_class($value);
            }
        }
        return null;
    }

    private function detectTenant(): ?string
    {
        try {
            if (tenancy()->initialized) {
                return tenant('id');
            }
        } catch (\Throwable $e) {}
        return null;
    }
}

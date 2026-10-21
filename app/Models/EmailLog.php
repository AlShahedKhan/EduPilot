<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    protected $table = 'email_logs';

    protected $fillable = [
        'from',
        'to',
        'subject',
        'body',
        'status',
        'error',
        'mailable_class',
        'tenant_id',
    ];
}

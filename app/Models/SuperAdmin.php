<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class SuperAdmin extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $table = 'super_admins';

    protected $fillable = [
        'name',
        'email',
        'designation',
        'password',
        'user_information',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getUserInformationAttribute($value): array
    {
        $data = $value;

        if (is_string($data)) {
            $decoded = json_decode($data, true);

            if (is_string($decoded)) {
                $decoded = json_decode($decoded, true);
            }

            $data = $decoded;
        }

        return is_array($data) ? $data : [];
    }
}

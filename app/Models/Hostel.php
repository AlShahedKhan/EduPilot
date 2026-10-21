<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hostel extends Model
{
    use HasFactory;
    protected $fillable = [
        'name', 'address', 'type', 'warden_id', 'school_id',
    ];

    public function rooms()
    {
        return $this->hasMany(HostelRoom::class);
    }

    public function warden(): BelongsTo
    {
        return $this->belongsTo(User::class, 'warden_id');
    }

}

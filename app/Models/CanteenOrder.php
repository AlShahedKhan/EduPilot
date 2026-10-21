<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanteenOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'student_id', 'order_number', 'total_amount',
        'status', 'note', 'ordered_by', 'timestamp',
    ];

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function orderedByUser()
    {
        return $this->belongsTo(User::class, 'ordered_by');
    }

    public function orderItems()
    {
        return $this->hasMany(CanteenOrderItem::class, 'order_id');
    }
}

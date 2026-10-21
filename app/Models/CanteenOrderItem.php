<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanteenOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'order_id', 'item_id', 'item_name',
        'item_price', 'quantity', 'subtotal',
    ];

    public function order()
    {
        return $this->belongsTo(CanteenOrder::class, 'order_id');
    }

    public function item()
    {
        return $this->belongsTo(CanteenItem::class, 'item_id');
    }
}

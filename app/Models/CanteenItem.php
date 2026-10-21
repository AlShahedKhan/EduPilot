<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanteenItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id', 'category_id', 'name', 'description',
        'price', 'stock_quantity', 'image', 'status',
    ];

    public function category()
    {
        return $this->belongsTo(CanteenCategory::class, 'category_id');
    }

    public function orderItems()
    {
        return $this->hasMany(CanteenOrderItem::class, 'item_id');
    }
}

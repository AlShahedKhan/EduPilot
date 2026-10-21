<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CanteenCategory extends Model
{
    use HasFactory;

    protected $fillable = ['school_id', 'name', 'status'];

    public function items()
    {
        return $this->hasMany(CanteenItem::class, 'category_id');
    }

    public function activeItems()
    {
        return $this->hasMany(CanteenItem::class, 'category_id')
                    ->where('status', 1)
                    ->where('stock_quantity', '>', 0);
    }
}

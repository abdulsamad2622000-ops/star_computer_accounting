<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'stock_code',
        'vendor_id',
        'purchase_price',
        'sale_price',
        'received_qty',
        'sold_qty',
        'remaining_qty',
        'alert_qty',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
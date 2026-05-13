<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockReturn extends Model
{
    protected $table = 'returns';

    protected $fillable = [
        'sale_id',
        'product_id',
        'sale_item_id',
        'type',
        'qty',
        'rate',
        'total',
        'reason',
        'date',
        'user_id',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function saleItem()
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
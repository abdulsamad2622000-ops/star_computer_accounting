<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'memo_no', 'type', 'customer_id', 'vendor_id',
        'user_id', 'date', 'subtotal', 'discount',
        'total', 'paid', 'balance',
        'payment_type', 'description'
    ];

    public function customer() {
        return $this->belongsTo(Customer::class);
    }
    public function vendor() {
        return $this->belongsTo(Vendor::class);
    }
    public function items() {
        return $this->hasMany(SaleItem::class);
    }
    public function salesperson() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
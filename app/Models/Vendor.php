<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name', 'contact1', 'contact2',
        'address', 'cnic', 'opening_balance', 'balance'
    ];

    public function purchases() {
        return $this->hasMany(Sale::class);
    }
    public function payments() {
        return $this->hasMany(VendorPayment::class);
    }
}

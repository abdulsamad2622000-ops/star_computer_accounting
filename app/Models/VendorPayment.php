<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorPayment extends Model
{
    protected $fillable = [
        'vendor_id',
        'date',
        'amount',
        'method',
        'platform',
        'account_number',
        'account_title',
        'cheque_no',
        'cheque_date',
        'bank_name',
        'note',
    ];

    protected $casts = [
        'date'        => 'date',
        'cheque_date' => 'date',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerPayment extends Model
{
    protected $fillable = [
        'customer_id',
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

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessBank extends Model
{
    protected $fillable = [
        'bank_name',
        'account_title',
        'account_number',
        'iban',
        'qr_code',
    ];
}
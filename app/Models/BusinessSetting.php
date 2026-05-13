<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessSetting extends Model
{
    protected $fillable = [
        'business_name', 'tagline', 'address',
        'contact1_name', 'contact1_phone',
        'contact2_name', 'contact2_phone',
        'contact3_name', 'contact3_phone',
        'bank_name', 'bank_account_title',
        'bank_account_number', 'bank_iban',
         'ntn',
        'notes', 'thank_you_message',
    ];


}
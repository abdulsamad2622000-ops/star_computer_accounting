<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class BusinessSettingSeeder extends Seeder
{
    public function run()
    {
        BusinessSetting::create([
            'business_name'       => 'Star Computer',
            'tagline'             => 'Deal in All Computer Products',
            'address'             => 'Office # 269, 2nd Floor, Regal Trade Square, Saddar, Karachi',
            'contact1_name'       => 'Rehan',
            'contact1_phone'      => '+92 3XX XXXXXXX',
            'contact2_name'       => 'Faraz',
            'contact2_phone'      => '+92 3XX XXXXXXX',
            'contact3_name'       => 'A.R',
            'contact3_phone'      => '+92 3XX XXXXXXX',
            'bank_name'           => 'Meezan Bank',
            'bank_account_title'  => 'STAR COMPUTER',
            'bank_account_number' => '01780104703562',
            'bank_iban'           => 'PK28MEZN000178010470356',
            'notes'               => "No Warranty of Display\nNo Burn & Damage Warranty\nDay's Return (without any Deduction)\nDay's Checking Warranty",
            'thank_you_message'   => 'Thank you for your business. Powered by STAR COMPUTER POS',
        ]);
    }
}
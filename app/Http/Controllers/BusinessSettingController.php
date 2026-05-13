<?php

namespace App\Http\Controllers;

use App\Models\BusinessSetting;
use App\Models\BusinessContact;
use App\Models\BusinessBank;
use Illuminate\Http\Request;

class BusinessSettingController extends Controller
{
    public function edit()
    {
        $setting  = BusinessSetting::first();
        $contacts = BusinessContact::all();
        $banks    = BusinessBank::all();

        return view('settings.business', compact(
            'setting', 'contacts', 'banks'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'business_name' => 'required',
        ]);

        // Business Settings update
        $setting = BusinessSetting::first();
        $setting->update([
            'business_name'      => $request->business_name,
            'tagline'            => $request->tagline,
            'address'            => $request->address,
            'ntn'                => $request->ntn,
            'notes'              => $request->notes,
            'thank_you_message'  => $request->thank_you_message,
        ]);

        // Contacts update
        BusinessContact::truncate();
        if ($request->contacts) {
            foreach ($request->contacts as $contact) {
                if (!empty($contact['name']) && !empty($contact['phone'])) {
                    BusinessContact::create([
                        'name'  => $contact['name'],
                        'phone' => $contact['phone'],
                    ]);
                }
            }
        }

        // Banks update
        if ($request->banks) {
            foreach ($request->banks as $i => $bank) {
                if (empty($bank['bank_name'])) continue;

                $existing = BusinessBank::find($bank['id'] ?? null);

                $qrCode = null;

                // QR Code upload
                if (isset($bank['qr_file']) && $request->hasFile("banks.{$i}.qr_file")) {
                    $file   = $request->file("banks.{$i}.qr_file");
                    $qrCode = $file->store('qr_codes', 'public');

                    if ($existing && $existing->qr_code) {
                        \Storage::disk('public')->delete($existing->qr_code);
                    }
                }

                if ($existing) {
                    $existing->update([
                        'bank_name'      => $bank['bank_name'],
                        'account_title'  => $bank['account_title'] ?? '',
                        'account_number' => $bank['account_number'] ?? '',
                        'iban'           => $bank['iban'] ?? null,
                        'qr_code'        => $qrCode ?? $existing->qr_code,
                    ]);
                } else {
                    BusinessBank::create([
                        'bank_name'      => $bank['bank_name'],
                        'account_title'  => $bank['account_title'] ?? '',
                        'account_number' => $bank['account_number'] ?? '',
                        'iban'           => $bank['iban'] ?? null,
                        'qr_code'        => $qrCode,
                    ]);
                }
            }
        }

        // Delete removed banks
        if ($request->delete_banks) {
            foreach ($request->delete_banks as $bankId) {
                $bank = BusinessBank::find($bankId);
                if ($bank) {
                    if ($bank->qr_code) {
                        \Storage::disk('public')->delete($bank->qr_code);
                    }
                    $bank->delete();
                }
            }
        }

        return redirect()->route('settings.business')
            ->with('success', '✅ Settings update ho gayi!');
    }
}
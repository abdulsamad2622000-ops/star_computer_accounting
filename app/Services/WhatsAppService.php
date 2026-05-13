<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $token;
    protected $phoneId;
    protected $version;
    protected $baseUrl;

    public function __construct()
    {
        $this->token    = config('services.whatsapp.token');
        $this->phoneId  = config('services.whatsapp.phone_id');
        $this->version  = config('services.whatsapp.version', 'v18.0');
        $this->baseUrl  = "https://graph.facebook.com/{$this->version}/{$this->phoneId}/messages";
    }

    // =============================
    // TEXT MESSAGE SEND
    // =============================
    public function sendText(string $phone, string $message): bool
    {
        try {
            $phone = $this->formatPhone($phone);

            $response = Http::withToken($this->token)
                ->post($this->baseUrl, [
                    'messaging_product' => 'whatsapp',
                    'to'                => $phone,
                    'type'              => 'text',
                    'text'              => [
                        'body' => $message
                    ]
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp sent', [
                    'to'      => $phone,
                    'message' => $message
                ]);
                return true;
            }

            Log::error('WhatsApp failed', [
                'response' => $response->json()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp exception', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // =============================
    // PDF DOCUMENT SEND
    // =============================
    public function sendDocument(
        string $phone,
        string $documentUrl,
        string $filename,
        string $caption = ''
    ): bool {
        try {
            $phone = $this->formatPhone($phone);

            $response = Http::withToken($this->token)
                ->post($this->baseUrl, [
                    'messaging_product' => 'whatsapp',
                    'to'                => $phone,
                    'type'              => 'document',
                    'document'          => [
                        'link'     => $documentUrl,
                        'filename' => $filename,
                        'caption'  => $caption
                    ]
                ]);

            if ($response->successful()) {
                Log::info('WhatsApp document sent', [
                    'to'       => $phone,
                    'filename' => $filename
                ]);
                return true;
            }

            Log::error('WhatsApp document failed', [
                'response' => $response->json()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('WhatsApp document exception', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    // =============================
    // SALE INVOICE SEND
    // =============================
    public function sendInvoice($sale): bool
    {
        if (!$sale->customer || !$sale->customer->contact1) {
            Log::warning('WhatsApp: Customer mobile nahi hai', [
                'sale_id' => $sale->id
            ]);
            return false;
        }

        $phone = $sale->customer->contact1;

        // Text message
        $message = $this->buildInvoiceMessage($sale);
        $this->sendText($phone, $message);

        // PDF send (agar public URL available ho)
        $pdfUrl = url("/sales/{$sale->id}/invoice/pdf");
        $this->sendDocument(
            $phone,
            $pdfUrl,
            "Invoice-{$sale->memo_no}.pdf",
            "Invoice #{$sale->memo_no} — Star Computer"
        );

        return true;
    }

    // =============================
    // VENDOR PURCHASE SEND
    // =============================
    public function sendPurchaseToVendor($purchase): bool
    {
        if (!$purchase->vendor || !$purchase->vendor->contact1) {
            Log::warning('WhatsApp: Vendor mobile nahi hai', [
                'purchase_id' => $purchase->id
            ]);
            return false;
        }

        $phone   = $purchase->vendor->contact1;
        $message = $this->buildPurchaseMessage($purchase);

        return $this->sendText($phone, $message);
    }

    // =============================
    // CUSTOMER LEDGER SEND
    // =============================
    public function sendLedger($customer): bool
    {
        if (!$customer->contact1) {
            return false;
        }

        $message = "Dear {$customer->name},\n\n";
        $message .= "Aapka ledger statement:\n";
        $message .= "Balance: Rs. " . number_format($customer->balance) . "\n\n";
        $message .= "— Star Computer";

        return $this->sendText($customer->contact1, $message);
    }

    // =============================
    // INVOICE MESSAGE BUILD
    // =============================
    private function buildInvoiceMessage($sale): string
    {
        $items = $sale->items->map(fn($item) =>
            "• {$item->product->name} x{$item->qty} = Rs. " .
            number_format($item->total)
        )->implode("\n");

        $message  = "🧾 *STAR COMPUTER*\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "Invoice #{$sale->memo_no}\n";
        $message .= "Date: {$sale->date}\n\n";
        $message .= "*Items:*\n{$items}\n\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "Subtotal: Rs. " . number_format($sale->subtotal) . "\n";

        if ($sale->discount > 0) {
            $message .= "Discount: Rs. " . number_format($sale->discount) . "\n";
        }

        $message .= "*Total: Rs. " . number_format($sale->total) . "*\n";
        $message .= "Cash: Rs. " . number_format($sale->paid) . "\n";

        if ($sale->balance > 0) {
            $message .= "⚠️ Credit: Rs. " . number_format($sale->balance) . "\n";
        }

        $message .= "\nShukriya! — Star Computer 🌟";

        return $message;
    }

    // =============================
    // PURCHASE MESSAGE BUILD
    // =============================
    private function buildPurchaseMessage($purchase): string
    {
        $items = $purchase->items->map(fn($item) =>
            "• {$item->product->name} x{$item->qty} = Rs. " .
            number_format($item->total)
        )->implode("\n");

        $message  = "🛒 *STAR COMPUTER — Purchase Order*\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "Memo #{$purchase->memo_no}\n";
        $message .= "Date: {$purchase->date}\n\n";
        $message .= "*Items:*\n{$items}\n\n";
        $message .= "━━━━━━━━━━━━━━━\n";
        $message .= "*Total: Rs. " . number_format($purchase->total) . "*\n";
        $message .= "Paid: Rs. " . number_format($purchase->paid) . "\n";

        if ($purchase->balance > 0) {
            $message .= "⚠️ Payable: Rs. " . number_format($purchase->balance) . "\n";
        }

        $message .= "\n— Star Computer 🌟";

        return $message;
    }

    // =============================
    // PHONE FORMAT
    // =============================
    private function formatPhone(string $phone): string
    {
        // Remove spaces, dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);

        // 03xx → 923xx (Pakistan)
        if (str_starts_with($phone, '0')) {
            $phone = '92' . substr($phone, 1);
        }

        // Agar + ho to hata do
        $phone = ltrim($phone, '+');

        return $phone;
    }
}
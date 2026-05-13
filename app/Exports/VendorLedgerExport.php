<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\VendorPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class VendorLedgerExport implements FromCollection, WithHeadings, WithStyles
{
    protected $vendor;

    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }

    public function headings(): array
    {
        return [
            'Date', 'Memo #', 'Type', 'Item',
            'Stock Code', 'Qty', 'Bill Amount',
            'Paid', 'Method', 'Reference', 'Description'
        ];
    }

    public function collection()
    {
        $rows = collect();

        // Purchase rows
        $purchases = Sale::where('vendor_id', $this->vendor->id)
            ->where('type', 'purchase')
            ->with('items.product')
            ->latest()
            ->get();

        foreach ($purchases as $purchase) {
            foreach ($purchase->items as $i => $item) {
                $rows->push([
                    'date'        => \Carbon\Carbon::parse($purchase->date)->format('d-m-Y'),
                    'memo_no'     => $purchase->memo_no,
                    'type'        => 'Purchase',
                    'item'        => $item->product->name ?? '—',
                    'stock_code'  => $item->stock_code ?? '—',
                    'qty'         => $item->qty,
                    'bill_amount' => $item->total,
                    'paid'        => $i === 0 ? $purchase->paid : '—',
                    'method'      => '—',
                    'reference'   => '—',
                    'description' => $item->description ?? '—',
                ]);
            }
        }

        // Payment rows
        $payments = VendorPayment::where('vendor_id', $this->vendor->id)
            ->latest()
            ->get();

        foreach ($payments as $payment) {
            $ref = '—';
            if ($payment->method === 'online') {
                $ref = $payment->platform . ' — ' . $payment->account_number;
            } elseif ($payment->method === 'cheque') {
                $ref = $payment->bank_name . ' #' . $payment->cheque_no;
            }

            $rows->push([
                'date'        => \Carbon\Carbon::parse($payment->date)->format('d-m-Y'),
                'memo_no'     => 'PAY',
                'type'        => 'Payment',
                'item'        => '—',
                'stock_code'  => '—',
                'qty'         => '—',
                'bill_amount' => '—',
                'paid'        => $payment->amount,
                'method'      => ucfirst($payment->method),
                'reference'   => $ref,
                'description' => $payment->note ?? '—',
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'color' => ['rgb' => '163A6F']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }
}

<?php

namespace App\Exports;

use App\Models\Sale;
use App\Models\CustomerPayment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerLedgerExport implements FromCollection, WithHeadings, WithStyles
{
    protected $customer;

    public function __construct($customer)
    {
        $this->customer = $customer;
    }

    public function headings(): array
    {
        return [
            'Date', 'Memo #', 'Type', 'Item',
            'Stock Code', 'Qty', 'Bill Amount',
            'Received', 'Method', 'Reference', 'Description'
        ];
    }

    public function collection()
    {
        $rows = collect();

        // Sale rows
        $sales = Sale::where('customer_id', $this->customer->id)
            ->where('type', 'sale')
            ->with('items.product')
            ->latest()
            ->get();

        foreach ($sales as $sale) {
            foreach ($sale->items as $i => $item) {
                $rows->push([
                    'date'        => \Carbon\Carbon::parse($sale->date)->format('d-m-Y'),
                    'memo_no'     => $sale->memo_no,
                    'type'        => 'Sale',
                    'item'        => $item->product->name ?? '—',
                    'stock_code'  => $item->stock_code ?? '—',
                    'qty'         => $item->qty,
                    'bill_amount' => $item->total,
                    'received'    => $i === 0 ? $sale->paid : '—',
                    'method'      => '—',
                    'reference'   => '—',
                    'description' => $item->description ?? '—',
                ]);
            }
        }

        // Payment rows
        $payments = CustomerPayment::where('customer_id', $this->customer->id)
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
                'received'    => $payment->amount,
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
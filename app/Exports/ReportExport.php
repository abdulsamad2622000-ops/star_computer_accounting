<?php

namespace App\Exports;

use App\Models\Sale;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReportExport implements FromCollection, WithHeadings, WithStyles
{
    protected $from;
    protected $to;
    protected $name;

    public function __construct($from, $to, $name = null)
    {
        $this->from = $from;
        $this->to   = $to;
        $this->name = $name;
    }

    public function collection()
    {
        $sales = Sale::where('type', 'sale')
            ->whereBetween('date', [$this->from, $this->to])
            ->with(['customer', 'items.product', 'salesperson'])
            ->when($this->name, fn($q) =>
                $q->whereHas('customer', fn($q) =>
                    $q->where('name', 'like', '%'.$this->name.'%')
                )
            )
            ->get();

        $rows = collect();

        foreach ($sales as $sale) {
            foreach ($sale->items as $item) {
                $rows->push([
                    'Date'        => $sale->date,
                    'Memo #'      => $sale->memo_no,
                    'Customer'    => $sale->customer->name ?? '—',
                    'Item'        => $item->product->name ?? '—',
                    'Stock Code'  => $item->stock_code ?? '—',
                    'Qty'         => $item->qty,
                    'Bill Amount' => $item->total,
                    'Received'    => $loop->first ?? false
                        ? $sale->paid : 0,
                    'Description' => $sale->description ?? '—',
                    'Salesperson' => $sale->salesperson->name ?? '—',
                ]);
            }
        }

        return $rows;
    }

    public function headings(): array
    {
        return [
            'Date', 'Memo #', 'Customer', 'Item',
            'Stock Code', 'Qty', 'Bill Amount',
            'Received', 'Description', 'Salesperson'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => 'solid',
                    'startColor' => ['rgb' => '1a1f2e']
                ],
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF']
                ]
            ],
        ];
    }
}
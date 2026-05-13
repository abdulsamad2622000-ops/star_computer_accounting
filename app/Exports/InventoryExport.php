<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithStyles
{
    public function headings(): array
    {
        return [
            '#', 'Stock Code', 'Product', 'Vendor',
            'Received', 'Sold', 'Remaining',
            'Purchase Price', 'Sale Price'
        ];
    }

    public function collection()
    {
        $products = Product::with('vendor')->get();
        $rows     = collect();

        foreach ($products as $i => $product) {
            $rows->push([
                $i + 1,
                $product->stock_code ?? '—',
                $product->name,
                $product->vendor->name ?? '—',
                $product->received_qty,
                $product->sold_qty,
                $product->remaining_qty,
                $product->purchase_price,
                $product->sale_price,
            ]);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['rgb' => 'FFFFFF']
                ],
                'fill' => [
                    'fillType' => 'solid',
                    'color'    => ['rgb' => '163A6F']
                ],
            ],
        ];
    }
}
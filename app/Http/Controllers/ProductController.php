<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Vendor;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products   = Product::with('vendor')->get();
        $vendors    = Vendor::orderBy('name')->get();
        $totalValue = $products->where('is_active', true)
            ->sum(fn($p) => $p->remaining_qty * $p->purchase_price);
        $lowStock   = $products->where('is_active', true)
            ->filter(fn($p) => $p->remaining_qty <= $p->alert_qty)
            ->count();

        return view('products.index', compact(
            'products', 'vendors', 'totalValue', 'lowStock'
        ));
    }

    public function edit(Product $product)
    {
        $vendors = Vendor::all();
        return view('products.edit', compact('product', 'vendors'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name'          => 'required|string|unique:products,name,' . $product->id,
            'received_qty'  => 'nullable|integer|min:0',
            'remaining_qty' => 'nullable|integer|min:0',
        ]);

        if ($request->stock_code) {
            $exists = Product::where('stock_code', $request->stock_code)
                ->where('id', '!=', $product->id)
                ->exists();
            if ($exists) {
                return back()->withErrors([
                    'stock_code' => '⚠️ Yeh Stock Code already exist karta hai!'
                ]);
            }
        }

        $product->update([
            'name'           => $request->name,
            'stock_code'     => $request->stock_code     ?? null,
            'vendor_id'      => $request->vendor_id      ?? null,
            'purchase_price' => $request->purchase_price ?? $product->purchase_price,
            'sale_price'     => $request->sale_price     ?? $product->sale_price,
            'alert_qty'      => $request->alert_qty      ?? $product->alert_qty,
            'received_qty'   => $request->received_qty   ?? $product->received_qty,
            'remaining_qty'  => $request->remaining_qty  ?? $product->remaining_qty,
        ]);

        return redirect()->route('products.index')
            ->with('success', '✅ Product update ho gaya!');
    }

    public function destroy(Product $product)
    {
        $product->update(['is_active' => !$product->is_active]);

        $msg = $product->is_active
            ? '✅ Product enable ho gaya!'
            : '🚫 Product disable ho gaya!';

        return redirect()->route('products.index')
            ->with('success', $msg);
    }

    public function openingStore(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|unique:products,name',
            'opening_qty'    => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
        ]);

        if ($request->stock_code) {
            $existing = Product::where('stock_code', $request->stock_code)
                ->first();
            if ($existing) {
                $existing->increment('received_qty', $request->opening_qty);
                $existing->increment('remaining_qty', $request->opening_qty);
                $existing->update([
                    'purchase_price' => $request->purchase_price,
                    'sale_price'     => $request->sale_price ?? $existing->sale_price,
                    'is_active'      => true,
                ]);

                return redirect()->route('products.index')
                    ->with('success', '✅ Stock updated — ' . $existing->name);
            }
        }

        Product::create([
            'name'           => $request->name,
            'stock_code'     => $request->stock_code   ?? null,
            'vendor_id'      => $request->vendor_id    ?? null,
            'purchase_price' => $request->purchase_price,
            'sale_price'     => $request->sale_price   ?? 0,
            'received_qty'   => $request->opening_qty,
            'sold_qty'       => 0,
            'remaining_qty'  => $request->opening_qty,
            'alert_qty'      => $request->alert_qty    ?? 5,
            'is_active'      => true,
        ]);

        return redirect()->route('products.index')
            ->with('success', '✅ Opening stock add ho gaya!');
    }

    public function exportPdf()
    {
        $products = Product::with('vendor')->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'products.pdf',
            compact('products')
        );
        return $pdf->download('Inventory.pdf');
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\InventoryExport(),
            'Inventory.xlsx'
        );
    }
}
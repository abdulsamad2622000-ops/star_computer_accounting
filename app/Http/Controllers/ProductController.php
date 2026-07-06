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

    // ✅ NEW: Search/Autocomplete API
    public function search(Request $request)
    {
        $query = $request->get('q', '');

        $products = Product::with('vendor')
            ->where('is_active', true)
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', '%' . $query . '%')
                  ->orWhere('stock_code', 'like', '%' . $query . '%');
            })
            ->orderBy('name')
            ->limit(10)
            ->get()
            ->map(fn($p) => [
                'id'             => $p->id,
                'name'           => $p->name,
                'stock_code'     => $p->stock_code,
                'vendor'         => $p->vendor->name ?? '—',
                'remaining_qty'  => $p->remaining_qty,
                'purchase_price' => $p->purchase_price,
                'sale_price'     => $p->sale_price,
            ]);

        return response()->json($products);
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
    // Agar delete request hai
    if (request('action') === 'delete') {
        $salesCount = \App\Models\SaleItem::where('product_id', $product->id)->count();

        if ($product->remaining_qty > 0) {
            return redirect()->route('products.index')
                ->with('error', '❌ Delete nahi ho sakta — pehle stock zero karo!');
        }

        if ($salesCount > 0) {
            return redirect()->route('products.index')
                ->with('error', '❌ Delete nahi ho sakta — is product ki ' . $salesCount . ' sale/purchase records hain!');
        }

        $product->delete();
        return redirect()->route('products.index')
            ->with('success', '🗑️ Product permanently delete ho gaya!');
    }

    // Warna inactive/active toggle
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
            'name'           => 'required|string',
            'opening_qty'    => 'required|integer|min:0',
            'purchase_price' => 'required|numeric|min:0',
        ]);

        $newQty   = (int) $request->opening_qty;
        $newPrice = (float) $request->purchase_price;

        // ✅ Duplicate check by NAME (case-insensitive)
        $existing = Product::whereRaw('LOWER(name) = ?', [strtolower(trim($request->name))])->first();

        // ✅ Also check by stock_code if provided
        if (!$existing && $request->stock_code) {
            $existing = Product::where('stock_code', $request->stock_code)->first();
        }

       if ($existing) {
    $oldQty   = $existing->remaining_qty; // stock jo abhi hai
    $oldPrice = $existing->purchase_price;

    // ✅ Agar remaining zero hai to bhi average sahi rahe
    if ($oldQty <= 0) {
        $avgPrice = $newPrice; // koi purana stock nahi, naya price hi average
    } else {
        $avgPrice = (($oldQty * $oldPrice) + ($newQty * $newPrice)) / ($oldQty + $newQty);
    }

    $existing->update([
        'received_qty'   => $existing->received_qty + $newQty,
        'remaining_qty'  => $existing->remaining_qty + $newQty,
        'purchase_price' => round($avgPrice, 2),
        'sale_price'     => $request->sale_price    ?? $existing->sale_price,
        'stock_code'     => $request->stock_code    ?? $existing->stock_code,
        'vendor_id'      => $request->vendor_id     ?? $existing->vendor_id,
        'is_active'      => true,
    ]);

    return redirect()->route('products.index')
        ->with('success', '✅ Stock updated! ' . $existing->name . 
               ' — Qty +' . $newQty . 
               ' | Avg Price: Rs. ' . number_format($avgPrice, 2));
}
        // ✅ New product — create fresh
        Product::create([
            'name'           => trim($request->name),
            'stock_code'     => $request->stock_code ?? null,
            'vendor_id'      => $request->vendor_id  ?? null,
            'purchase_price' => $newPrice,
            'sale_price'     => $request->sale_price ?? 0,
            'received_qty'   => $newQty,
            'sold_qty'       => 0,
            'remaining_qty'  => $newQty,
            'alert_qty'      => $request->alert_qty  ?? 5,
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
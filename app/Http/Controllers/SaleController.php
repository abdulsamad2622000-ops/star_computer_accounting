<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\WhatsAppService;

class SaleController extends Controller
{
    public function pos()
    {
        $customers = Customer::orderBy('name')->get();
        $products  = Product::where('remaining_qty', '>', 0)
            ->where('is_active', true)
            ->get();
        $staff    = User::orderBy('name')->get();
        $contacts = \App\Models\BusinessContact::all();
        $banks    = \App\Models\BusinessBank::all();

        $sales = Sale::where('type', 'sale')
            ->whereDate('date', today())
            ->with(['customer', 'items.product', 'salesperson'])
            ->latest()
            ->get();

        return view('sales.pos', compact(
            'customers', 'products', 'staff', 'sales',
            'contacts', 'banks'
        ));
    }

    public function history(Request $request)
    {
        $query = Sale::where('type', 'sale')
            ->with(['customer', 'items.product', 'salesperson']);

        if ($request->all == '1') {
            // koi date filter nahi
        } elseif ($request->from && $request->to) {
            $query->whereBetween('date', [$request->from, $request->to]);
        } elseif ($request->from) {
            $query->whereDate('date', '>=', $request->from);
        } elseif ($request->to) {
            $query->whereDate('date', '<=', $request->to);
        } elseif ($request->date) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }

        if ($request->memo_no) {
            $query->where('memo_no', 'like', '%'.$request->memo_no.'%');
        }

        if ($request->customer_name) {
            $query->whereHas('customer', fn($q) =>
                $q->where('name', 'like', '%'.$request->customer_name.'%')
            );
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id'        => 'required|exists:customers,id',
            'user_id'            => 'required|exists:users,id',
            'date'               => 'required|date',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'items.*.rate'       => 'required|numeric|min:0',
        ]);

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->remaining_qty < $item['qty']) {
                return response()->json([
                    'success' => false,
                    'message' => "⚠️ {$product->name} ka stock kam hai! Available: {$product->remaining_qty}"
                ], 422);
            }
        }

        DB::transaction(function () use ($request, &$sale) {

            do {
                $memoNo = rand(100000, 999999);
            } while (Sale::where('memo_no', $memoNo)->exists());

            $subtotal = collect($request->items)
                ->sum(fn($i) => $i['qty'] * $i['rate']);
            $discount = $request->discount ?? 0;
            $total    = $subtotal - $discount;
            $paid     = $request->paid ?? 0;
            $balance  = $total - $paid;

            if ($paid == 0) {
                $paymentType = 'credit';
            } elseif ($paid >= $total) {
                $paymentType = 'cash';
                $paid        = $total;
                $balance     = 0;
            } else {
                $paymentType = 'partial';
            }

            $sale = Sale::create([
                'memo_no'      => $memoNo,
                'type'         => 'sale',
                'customer_id'  => $request->customer_id,
                'user_id'      => $request->user_id,
                'date'         => $request->date,
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'total'        => $total,
                'paid'         => $paid,
                'balance'      => $balance,
                'payment_type' => $paymentType,
                'description'  => $request->description ?? null,
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);

                SaleItem::create([
                    'sale_id'     => $sale->id,
                    'product_id'  => $item['product_id'],
                    'stock_code'  => $product->stock_code,
                    'qty'         => $item['qty'],
                    'rate'        => $item['rate'],
                    'total'       => $item['qty'] * $item['rate'],
                    'description' => $item['description'] ?? null,
                ]);

                $product->decrement('remaining_qty', $item['qty']);
                $product->increment('sold_qty', $item['qty']);
            }

            if ($balance > 0) {
                $sale->customer->increment('balance', $balance);
            }
        });

        try {
            $whatsapp = new WhatsAppService();
            $sale->load(['customer', 'items.product', 'salesperson']);
            $whatsapp->sendInvoice($sale);
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Bill ban gaya! ✅',
            'sale_id' => $sale->id,
            'memo_no' => $sale->memo_no,
        ]);
    }

    public function invoice(Sale $sale)
    {
        $sale->load(['customer', 'items.product', 'salesperson']);
        return view('sales.invoice', compact('sale'));
    }

    public function purchasePos()
    {
        $vendors  = Vendor::orderBy('name')->get();
        $staff    = User::orderBy('name')->get();
        $products = Product::where('is_active', true)
            ->orderBy('name')
            ->get();
        $contacts = \App\Models\BusinessContact::all();
        $banks    = \App\Models\BusinessBank::all();

        $purchases = Sale::where('type', 'purchase')
            ->whereDate('date', today())
            ->with(['vendor', 'items.product'])
            ->latest()
            ->get();

        return view('purchases.pos', compact(
            'vendors', 'staff', 'products', 'purchases',
            'contacts', 'banks'
        ));
    }

    public function purchaseStore(Request $request)
    {
        $request->validate([
            'vendor_id'              => 'required|exists:vendors,id',
            'user_id'                => 'required|exists:users,id',
            'date'                   => 'required|date',
            'items'                  => 'required|array|min:1',
            'items.*.name'           => 'required|string',
            'items.*.qty'            => 'required|integer|min:1',
            'items.*.purchase_price' => 'required|numeric|min:0',
            'items.*.sale_price'     => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($request, &$purchase) {

            do {
                $memoNo = rand(100000, 999999);
            } while (Sale::where('memo_no', $memoNo)->exists());

            $subtotal = collect($request->items)
                ->sum(fn($i) => $i['qty'] * $i['purchase_price']);
            $discount = $request->discount ?? 0;
            $total    = $subtotal - $discount;
            $paid     = $request->paid ?? 0;
            $balance  = $total - $paid;

            if ($paid == 0) {
                $paymentType = 'credit';
            } elseif ($paid >= $total) {
                $paymentType = 'cash';
                $paid        = $total;
                $balance     = 0;
            } else {
                $paymentType = 'partial';
            }

            $purchase = Sale::create([
                'memo_no'      => $memoNo,
                'type'         => 'purchase',
                'vendor_id'    => $request->vendor_id,
                'user_id'      => $request->user_id,
                'date'         => $request->date,
                'subtotal'     => $subtotal,
                'discount'     => $discount,
                'total'        => $total,
                'paid'         => $paid,
                'balance'      => $balance,
                'payment_type' => $paymentType,
                'description'  => $request->description ?? null,
            ]);

            foreach ($request->items as $item) {

                $newQty   = (int)   $item['qty'];
                $newPrice = (float) $item['purchase_price'];

                if (!empty($item['stock_code'])) {
                    // ── Stock code se dhundo ──────────────────────────
                    $existingProduct = Product::where('stock_code', $item['stock_code'])->first();

                    if ($existingProduct) {
                        // ✅ Average price formula
                        $oldQty   = $existingProduct->remaining_qty;
                        $oldPrice = $existingProduct->purchase_price;

                        $avgPrice = ($oldQty + $newQty) > 0
                            ? (($oldQty * $oldPrice) + ($newQty * $newPrice)) / ($oldQty + $newQty)
                            : $newPrice;

                        $existingProduct->increment('received_qty', $newQty);
                        $existingProduct->increment('remaining_qty', $newQty);
                        $existingProduct->update([
                            'purchase_price' => round($avgPrice, 2),
                            'sale_price'     => $item['sale_price'],
                            'vendor_id'      => $request->vendor_id,
                        ]);
                        $product = $existingProduct;

                    } else {
                        $product = Product::create([
                            'stock_code'     => $item['stock_code'],
                            'name'           => $item['name'],
                            'vendor_id'      => $request->vendor_id,
                            'purchase_price' => $newPrice,
                            'sale_price'     => $item['sale_price'],
                            'received_qty'   => $newQty,
                            'sold_qty'       => 0,
                            'remaining_qty'  => $newQty,
                            'alert_qty'      => $item['alert_qty'] ?? 5,
                            'is_active'      => true,
                        ]);
                    }

                } else {
                    // ── Naam se dhundo ────────────────────────────────
                    $existingProduct = Product::whereRaw('LOWER(name) = ?', [strtolower(trim($item['name']))])->first();

                    if ($existingProduct) {
                        // ✅ Average price formula
                        $oldQty   = $existingProduct->remaining_qty;
                        $oldPrice = $existingProduct->purchase_price;

                        $avgPrice = ($oldQty + $newQty) > 0
                            ? (($oldQty * $oldPrice) + ($newQty * $newPrice)) / ($oldQty + $newQty)
                            : $newPrice;

                        $existingProduct->increment('received_qty', $newQty);
                        $existingProduct->increment('remaining_qty', $newQty);
                        $existingProduct->update([
                            'purchase_price' => round($avgPrice, 2),
                            'sale_price'     => $item['sale_price'],
                            'vendor_id'      => $request->vendor_id,
                        ]);
                        $product = $existingProduct;

                    } else {
                        $product = Product::create([
                            'stock_code'     => null,
                            'name'           => trim($item['name']),
                            'vendor_id'      => $request->vendor_id,
                            'purchase_price' => $newPrice,
                            'sale_price'     => $item['sale_price'],
                            'received_qty'   => $newQty,
                            'sold_qty'       => 0,
                            'remaining_qty'  => $newQty,
                            'alert_qty'      => $item['alert_qty'] ?? 5,
                            'is_active'      => true,
                        ]);
                    }
                }

             // Sale store mein:
SaleItem::create([
    'sale_id'                 => $sale->id,
    'product_id'              => $item['product_id'],
    'stock_code'              => $product->stock_code,
    'qty'                     => $item['qty'],
    'rate'                    => $item['rate'],
    'purchase_price_at_time'  => $product->purchase_price, // ✅ save karo
    'total'                   => $item['qty'] * $item['rate'],
    'description'             => $item['description'] ?? null,
]);
            }

            if ($balance > 0) {
                $purchase->vendor->increment('balance', $balance);
            }
        });

        try {
            $whatsapp = new WhatsAppService();
            $purchase->load(['vendor', 'items.product']);
            $whatsapp->sendPurchaseToVendor($purchase);
        } catch (\Exception $e) {
            Log::error('WhatsApp vendor send failed: ' . $e->getMessage());
        }

        return response()->json([
            'success'     => true,
            'message'     => 'Purchase bill ban gaya! ✅',
            'purchase_id' => $purchase->id,
            'memo_no'     => $purchase->memo_no,
        ]);
    }

    public function purchaseHistory(Request $request)
    {
        $query = Sale::where('type', 'purchase')
            ->with(['vendor', 'items.product', 'salesperson']);

        if ($request->from && $request->to) {
            $query->whereBetween('date', [$request->from, $request->to]);
        } elseif ($request->from) {
            $query->whereDate('date', '>=', $request->from);
        } elseif ($request->to) {
            $query->whereDate('date', '<=', $request->to);
        } elseif ($request->date) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereDate('date', today());
        }

        if ($request->memo_no) {
            $query->where('memo_no', 'like', '%'.$request->memo_no.'%');
        }

        if ($request->vendor_name) {
            $query->whereHas('vendor', fn($q) =>
                $q->where('name', 'like', '%'.$request->vendor_name.'%')
            );
        }

        return response()->json($query->latest()->get());
    }

    public function purchaseInvoice(Sale $sale)
    {
        $sale->load(['vendor', 'items.product', 'salesperson']);
        return view('purchases.invoice', compact('sale'));
    }

    public function purchaseInvoicePdf(Sale $sale)
    {
        $sale->load(['vendor', 'items.product', 'salesperson']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'purchases.invoice-pdf',
            compact('sale')
        );
        return $pdf->download("Purchase-{$sale->memo_no}.pdf");
    }

    public function getPurchaseItems(Sale $sale)
    {
        $sale->load('items.product');
        return response()->json($sale);
    }

    public function updatePurchaseRates(Request $request, Sale $sale)
    {
        foreach ($request->items as $item) {
            $saleItem = SaleItem::find($item['id']);
            if ($saleItem) {
                $saleItem->update(['rate' => $item['purchase_price']]);

                if ($saleItem->product) {
                    // ✅ Average price calculate karo
                    $product  = $saleItem->product;
                    $oldQty   = $product->remaining_qty;
                    $oldPrice = $product->purchase_price;
                    $newQty   = $saleItem->qty;
                    $newPrice = (float) $item['purchase_price'];

                    $avgPrice = ($oldQty > 0)
                        ? (($oldQty * $oldPrice) + ($newQty * $newPrice)) / ($oldQty + $newQty)
                        : $newPrice;

                    $product->update([
                        'purchase_price' => round($avgPrice, 2),
                        'sale_price'     => $item['sale_price'],
                    ]);
                }
            }
        }

        $subtotal = $sale->items->sum(fn($i) => $i->qty * $i->rate);
        $total    = $subtotal - $sale->discount;
        $balance  = $total - $sale->paid;

        $sale->update([
            'subtotal' => $subtotal,
            'total'    => $total,
            'balance'  => $balance,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Rates update ho gayi!'
        ]);
    }

    public function transferSale(Request $request, $saleId)
    {
        $request->validate([
            'new_customer_id' => 'required|exists:customers,id',
        ]);

        $sale          = \App\Models\Sale::findOrFail($saleId);
        $oldCustomerId = $sale->customer_id;
        $newCustomerId = $request->new_customer_id;

        if ($oldCustomerId == $newCustomerId) {
            return response()->json([
                'success' => false,
                'message' => 'Same customer hai! Alag customer select karein.'
            ]);
        }

        $oldCustomer = \App\Models\Customer::find($oldCustomerId);
        $newCustomer = \App\Models\Customer::find($newCustomerId);

        if ($oldCustomer && $sale->balance > 0) {
            $oldCustomer->decrement('balance', $sale->balance);
        }
        if ($newCustomer && $sale->balance > 0) {
            $newCustomer->increment('balance', $sale->balance);
        }

        $sale->update(['customer_id' => $newCustomerId]);

        return response()->json([
            'success' => true,
            'message' => '✅ Bill transfer ho gaya!'
        ]);
    }

    public function transferPurchase(Request $request, $saleId)
    {
        $request->validate([
            'new_vendor_id' => 'required|exists:vendors,id',
        ]);

        $purchase    = \App\Models\Sale::findOrFail($saleId);
        $oldVendorId = $purchase->vendor_id;
        $newVendorId = $request->new_vendor_id;

        if ($oldVendorId == $newVendorId) {
            return response()->json([
                'success' => false,
                'message' => 'Same vendor hai! Alag vendor select karein.'
            ]);
        }

        $oldVendor = \App\Models\Vendor::find($oldVendorId);
        $newVendor = \App\Models\Vendor::find($newVendorId);

        if ($oldVendor && $purchase->balance > 0) {
            $oldVendor->decrement('balance', $purchase->balance);
        }
        if ($newVendor && $purchase->balance > 0) {
            $newVendor->increment('balance', $purchase->balance);
        }

        $purchase->update(['vendor_id' => $newVendorId]);

        return response()->json([
            'success' => true,
            'message' => '✅ Purchase transfer ho gaya!'
        ]);
    }

    public function updateSale(Request $request, $saleId)
    {
        $sale = \App\Models\Sale::with('items.product')->findOrFail($saleId);

        $request->validate([
            'date'         => 'required|date',
            'customer_id'  => 'required|exists:customers,id',
            'payment_type' => 'required',
            'items'        => 'required|array',
        ]);

        // Inventory reverse karo (old quantities)
        foreach ($sale->items as $oldItem) {
            if ($oldItem->product) {
                $oldItem->product->increment('remaining_qty', $oldItem->qty);
                $oldItem->product->decrement('sold_qty', $oldItem->qty);
            }
        }

        // Old customer balance reverse karo
        $oldCustomer = \App\Models\Customer::find($sale->customer_id);
        if ($oldCustomer && $sale->balance > 0) {
            $oldCustomer->decrement('balance', $sale->balance);
        }

        $total    = 0;
        $discount = $request->discount ?? 0;
        $paid     = $request->paid     ?? 0;

        $sale->items()->delete();

        foreach ($request->items as $item) {
            if (empty($item['product_id']) || empty($item['qty'])) continue;

            $product = \App\Models\Product::find($item['product_id']);
            if (!$product) continue;

            $qty  = (int)   $item['qty'];
            $rate = (float) $item['rate'];
            $amt  = $qty * $rate;
            $total += $amt;

            \App\Models\SaleItem::create([
                'sale_id'     => $sale->id,
                'product_id'  => $product->id,
                'stock_code'  => $item['stock_code'] ?? $product->stock_code,
                'qty'         => $qty,
                'rate'        => $rate,
                'total'       => $amt,
                'description' => $item['description'] ?? null,
            ]);

            $product->decrement('remaining_qty', $qty);
            $product->increment('sold_qty', $qty);
        }

        $netTotal = max($total - $discount, 0);
        $balance  = max($netTotal - $paid, 0);

        $sale->update([
            'customer_id'  => $request->customer_id,
            'date'         => $request->date,
            'total'        => $netTotal,
            'discount'     => $discount,
            'paid'         => $paid,
            'balance'      => $balance,
            'payment_type' => $request->payment_type,
        ]);

        $newCustomer = \App\Models\Customer::find($request->customer_id);
        if ($newCustomer && $balance > 0) {
            $newCustomer->increment('balance', $balance);
        }

        return response()->json([
            'success' => true,
            'message' => '✅ Sale update ho gaya!'
        ]);
    }

    public function editData($saleId)
    {
        $sale = \App\Models\Sale::with('items.product')->findOrFail($saleId);

        return response()->json([
            'success' => true,
            'sale'    => [
                'id'           => $sale->id,
                'date'         => $sale->date,
                'customer_id'  => $sale->customer_id,
                'payment_type' => $sale->payment_type,
                'discount'     => $sale->discount,
                'paid'         => $sale->paid,
                'balance'      => $sale->balance,
                'items'        => $sale->items->map(fn($item) => [
                    'id'          => $item->id,
                    'product_id'  => $item->product_id,
                    'stock_code'  => $item->stock_code,
                    'qty'         => $item->qty,
                    'rate'        => $item->rate,
                    'total'       => $item->total,
                    'description' => $item->description,
                ])
            ]
        ]);
    }

    public function editPurchaseData($saleId)
    {
        $sale = \App\Models\Sale::with('items.product')->findOrFail($saleId);

        return response()->json([
            'success' => true,
            'sale'    => [
                'id'           => $sale->id,
                'date'         => $sale->date,
                'vendor_id'    => $sale->vendor_id,
                'payment_type' => $sale->payment_type,
                'discount'     => $sale->discount,
                'paid'         => $sale->paid,
                'balance'      => $sale->balance,
                'items'        => $sale->items->map(fn($item) => [
                    'id'          => $item->id,
                    'product_id'  => $item->product_id,
                    'stock_code'  => $item->stock_code,
                    'qty'         => $item->qty,
                    'rate'        => $item->rate,
                    'total'       => $item->total,
                    'description' => $item->description,
                ])
            ]
        ]);
    }

    public function updatePurchase(Request $request, $saleId)
    {
        $sale = \App\Models\Sale::with('items.product')->findOrFail($saleId);

        // Inventory reverse karo
        foreach ($sale->items as $oldItem) {
            if ($oldItem->product) {
                $oldItem->product->increment('remaining_qty', $oldItem->qty);
                $oldItem->product->decrement('received_qty', $oldItem->qty);
            }
        }

        // Old vendor balance reverse karo
        $oldVendor = \App\Models\Vendor::find($sale->vendor_id);
        if ($oldVendor && $sale->balance > 0) {
            $oldVendor->decrement('balance', $sale->balance);
        }

        $total    = 0;
        $discount = $request->discount ?? 0;
        $paid     = $request->paid     ?? 0;

        $sale->items()->delete();

        foreach ($request->items as $item) {
            if (empty($item['product_id']) || empty($item['qty'])) continue;

            $product = \App\Models\Product::find($item['product_id']);
            if (!$product) continue;

            $qty      = (int)   $item['qty'];
            $rate     = (float) $item['rate'];
            $amt      = $qty * $rate;
            $total   += $amt;

            \App\Models\SaleItem::create([
                'sale_id'     => $sale->id,
                'product_id'  => $product->id,
                'stock_code'  => $item['stock_code'] ?? $product->stock_code,
                'qty'         => $qty,
                'rate'        => $rate,
                'total'       => $amt,
                'description' => $item['description'] ?? null,
            ]);

            // ✅ Average price calculate karo
            $oldQty   = $product->remaining_qty; // already reversed upar
            $oldPrice = $product->purchase_price;

            $avgPrice = ($oldQty + $qty) > 0
                ? (($oldQty * $oldPrice) + ($qty * $rate)) / ($oldQty + $qty)
                : $rate;

            $product->decrement('remaining_qty', $qty); // wait — pehle avg nikalo phir decrement
            // Note: upar reverse ho chuka hai, to remaining_qty = purana remaining + old item qty
            // Ab hum naya qty add kar rahe hain purchase mein
            $product->increment('received_qty', $qty);

            $updateData = ['purchase_price' => round($avgPrice, 2)];
            if (!empty($item['sale_price'])) {
                $updateData['sale_price'] = $item['sale_price'];
            }
            $product->update($updateData);
        }

        $netTotal = max($total - $discount, 0);
        $balance  = max($netTotal - $paid, 0);

        $sale->update([
            'vendor_id'    => $request->vendor_id,
            'date'         => $request->date,
            'total'        => $netTotal,
            'discount'     => $discount,
            'paid'         => $paid,
            'balance'      => $balance,
            'payment_type' => $request->payment_type,
        ]);

        $newVendor = \App\Models\Vendor::find($request->vendor_id);
        if ($newVendor && $balance > 0) {
            $newVendor->increment('balance', $balance);
        }

        return response()->json([
            'success' => true,
            'message' => '✅ Purchase update ho gaya!'
        ]);
    }
}
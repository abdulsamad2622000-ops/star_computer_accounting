<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\CustomerPayment;
use App\Models\StaffPermission;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Staff Permission Check ───────────────────────────
        if (auth()->user()->role !== 'admin') {
            $perms = StaffPermission::where('user_id', auth()->id())->first();
            if (!$perms || !$perms->dashboard_access) {
                return redirect()->route('sales.pos');
            }
        }

        // ── Customers & Vendors ──────────────────────────────
        $totalReceivable = Customer::sum('balance');
        $totalPayable    = Vendor::sum('balance');
        $totalCustomers  = Customer::count();

        // ── Products / Stock ─────────────────────────────────
        $totalProducts = Product::where('is_active', true)->count();

        $totalStockValue = Product::where('is_active', true)
            ->get()
            ->sum(fn($p) => $p->remaining_qty * $p->purchase_price);

        $lowStockCount = Product::where('is_active', true)
            ->whereColumn('remaining_qty', '<=', 'alert_qty')
            ->count();

        // ── Today's Sales ────────────────────────────────────
        $todaySales = Sale::where('type', 'sale')
            ->whereDate('date', today())
            ->get();

        $todayTotal    = $todaySales->sum('total');
        $todayInvoices = $todaySales->count();
        $todayReceived = $todaySales->sum('paid');
        $todayCredit   = $todaySales->sum('balance');
        $todayCash     = $todaySales->where('payment_type', 'cash')->sum('paid');

        // ── Total Cash Received (Customer se) ────────────────
        $totalCash = Sale::where('type', 'sale')
                        ->where('payment_type', 'cash')
                        ->sum('paid')
                   + CustomerPayment::where('method', 'cash')
                        ->sum('amount');

        // ── Total Online Received (Customer se) ──────────────
        $totalOnline = Sale::where('type', 'sale')
                          ->where('payment_type', 'bank_transfer')
                          ->sum('paid')
                     + CustomerPayment::where('method', 'online')
                          ->sum('amount');

        // ── Business Balance ─────────────────────────────────
        $businessBalance = $totalCash
                         + $totalOnline
                         + $totalStockValue
                         + $totalReceivable
                         - $totalPayable;

        // ── Below Cost Sales Loss ────────────────────────────
        $totalLoss = 0;
        $saleItems = SaleItem::whereHas('sale', fn($q) =>
            $q->where('type', 'sale')
        )->with('product')->get();

        foreach ($saleItems as $item) {
            $purchasePrice = $item->purchase_price ?? $item->product->purchase_price ?? 0;
            if ($item->rate > 0 && $item->rate < $purchasePrice) {
                $totalLoss += ($purchasePrice - $item->rate) * $item->qty;
            }
        }

        return view('dashboard', compact(
            'totalReceivable', 'totalPayable', 'totalCustomers',
            'totalProducts', 'totalStockValue', 'lowStockCount',
            'todayTotal', 'todayInvoices', 'todayReceived',
            'todayCredit', 'todayCash',
            'totalCash', 'totalOnline', 'businessBalance',
            'totalLoss'
        ));
    }
}
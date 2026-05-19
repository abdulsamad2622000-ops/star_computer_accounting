<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\CustomerPayment;
use App\Models\VendorPayment;
use App\Models\StaffPermission;

class DashboardController extends Controller
{
    public function index()
    {
        // Staff permission check
        if (auth()->user()->role !== 'admin') {
            $perms = StaffPermission::where('user_id', auth()->id())->first();
            if (!$perms || !$perms->dashboard_access) {
                return redirect()->route('sales.pos');
            }
        }

        $totalReceivable = Customer::sum('balance');
        $totalPayable    = Vendor::sum('balance');
        $totalCustomers  = Customer::count();

        $totalProducts   = Product::where('is_active', true)->count();
        $totalStockValue = Product::where('is_active', true)
            ->get()
            ->sum(fn($p) => $p->remaining_qty * $p->purchase_price);
        $lowStockCount   = Product::where('is_active', true)
            ->whereColumn('remaining_qty', '<=', 'alert_qty')
            ->count();

        $todaySales    = Sale::where('type', 'sale')
            ->whereDate('date', today())
            ->get();

        $todayTotal    = $todaySales->sum('total');
        $todayCount    = $todaySales->count();
        $todayInvoices = $todayCount;
        $todayReceived = $todaySales->sum('paid');
        $todayCredit   = $todaySales->sum('balance');
        $todayCash     = $todaySales
            ->where('payment_type', 'cash')
            ->sum('paid');

        // ── Balance Calculation ──────────────────────────────
        // Sales se cash payments
        $totalCashFromSales = Sale::where('type', 'sale')
            ->where('payment_type', 'cash')
            ->sum('paid');

        // Sales se online payments
        $totalOnlineFromSales = Sale::where('type', 'sale')
            ->where('payment_type', 'bank_transfer')
            ->sum('paid');

        // Customer payments (ledger) — cash
        $totalCashFromPayments = CustomerPayment::where('method', 'cash')
            ->sum('amount');

        // Customer payments (ledger) — online
        $totalOnlineFromPayments = CustomerPayment::where('method', 'online')
            ->sum('amount');

        $totalCash   = $totalCashFromSales   + $totalCashFromPayments;
        $totalOnline = $totalOnlineFromSales + $totalOnlineFromPayments;

        // ✅ SAHI formula:
        // Cash + Online + StockValue + Receivable - Payable
        $businessBalance = $totalCash
                         + $totalOnline
                         + $totalStockValue    // asset → PLUS
                         + $totalReceivable    // customer se lena → PLUS
                         - $totalPayable;      // vendor ko dena → MINUS

        // Total Loss calculation
      $totalLoss = 0;
$saleItems = SaleItem::whereHas('sale', fn($q) => 
    $q->where('type', 'sale')
)->with('product')->get();

foreach ($saleItems as $item) {
    $purchasePrice = $item->product->purchase_price ?? 0;
    $salePrice     = $item->rate;
    if ($salePrice > 0 && $salePrice < $purchasePrice) {
        $totalLoss += ($purchasePrice - $salePrice) * $item->qty;
    }
}

        return view('dashboard', compact(
            'totalReceivable', 'totalPayable', 'totalCustomers',
            'totalProducts', 'totalStockValue', 'lowStockCount',
            'todayTotal', 'todayCount', 'todayInvoices',
            'todayReceived', 'todayCredit', 'todayCash',
            'totalLoss',
            'totalCash', 'totalOnline', 'businessBalance'
        ));
    }
}
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
        // Sales se cash payments (payment_type = cash)
        $totalCashFromSales = Sale::where('type', 'sale')
            ->where('payment_type', 'cash')
            ->sum('paid');

        // Sales se online payments (payment_type = bank_transfer)
        $totalOnlineFromSales = Sale::where('type', 'sale')
            ->where('payment_type', 'bank_transfer')
            ->sum('paid');

        // Customer payments (ledger payments) — cash
        $totalCashFromPayments = CustomerPayment::where('method', 'cash')
            ->sum('amount');

        // Customer payments (ledger payments) — online
        $totalOnlineFromPayments = CustomerPayment::where('method', 'online')
            ->sum('amount');

        // Total cash aur online combine
        $totalCash   = $totalCashFromSales   + $totalCashFromPayments;
        $totalOnline = $totalOnlineFromSales + $totalOnlineFromPayments;

        // Final balance formula:
        // Cash + Online + Payable - Receivable + StockValue
        $businessBalance = $totalCash + $totalOnline
                         + $totalPayable
                         - $totalReceivable
                         + $totalStockValue;

        // Total Loss calculation
        $totalLoss = 0;
        $saleItems = SaleItem::with('product')->get();
        foreach ($saleItems as $item) {
            $purchasePrice = $item->product->purchase_price ?? 0;
            $salePrice     = $item->rate;
            if ($salePrice < $purchasePrice) {
                $totalLoss += ($purchasePrice - $salePrice) * $item->qty;
            }
        }

        return view('dashboard', compact(
            'totalReceivable', 'totalPayable', 'totalCustomers',
            'totalProducts', 'totalStockValue', 'lowStockCount',
            'todayTotal', 'todayCount', 'todayInvoices',
            'todayReceived', 'todayCredit', 'todayCash',
            'totalLoss',
            // ✅ Naye variables
            'totalCash', 'totalOnline', 'businessBalance'
        ));
    }
}
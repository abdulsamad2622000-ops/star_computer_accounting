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

        // ── Total Cash ───────────────────────────────────────
        // + Sales cash received
        // + Customer ledger cash payments
        // - Vendor ko cash diya
        $totalCash = Sale::where('type', 'sale')
                        ->where('payment_type', 'cash')
                        ->sum('paid')
                   + CustomerPayment::where('method', 'cash')
                        ->sum('amount')
                   - VendorPayment::where('method', 'cash')
                        ->sum('amount');

        // ── Total Online ─────────────────────────────────────
        // + Sales online received
        // + Customer ledger online payments
        // - Vendor ko online diya
        $totalOnline = Sale::where('type', 'sale')
                          ->where('payment_type', 'bank_transfer')
                          ->sum('paid')
                     + CustomerPayment::where('method', 'online')
                          ->sum('amount')
                     - VendorPayment::where('method', 'online')
                          ->sum('amount');

        // ── Business Balance ─────────────────────────────────
        //   + Total Cash      (hamare paas physical cash)
        //   + Total Online    (hamare paas bank mein)
        //   + Stock Value     (inventory asset)
        //   + Receivable      (customer se lena baaki)
        //   - Payable         (vendor ko dena baaki)
      $businessBalance = $totalStockValue
                 + $totalReceivable
                 - $totalPayable;

        // ── Below Cost Sales Loss ────────────────────────────
        // ── Below Cost Sales Loss (Current Month) ────────────
$totalLoss = 0;
$saleItems = SaleItem::whereHas('sale', fn($q) =>
    $q->where('type', 'sale')
     ->whereMonth('date', now()->month)
     ->whereYear('date', now()->year)
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


    public function lossData(Request $request)
{
    $filter = $request->get('filter', 'this_month');

    $query = \App\Models\SaleItem::whereHas('sale', fn($q) =>
        $q->where('type', 'sale')
    )->with(['sale.customer', 'product']);

    // Date filter
    if ($filter === 'this_month') {
        $query->whereHas('sale', fn($q) =>
            $q->whereMonth('date', now()->month)
              ->whereYear('date', now()->year)
        );
    } elseif ($filter === 'last_month') {
        $query->whereHas('sale', fn($q) =>
            $q->whereMonth('date', now()->subMonth()->month)
              ->whereYear('date', now()->subMonth()->year)
        );
    } elseif ($filter === 'custom') {
        $from = $request->get('from');
        $to   = $request->get('to');
        if ($from && $to) {
            $query->whereHas('sale', fn($q) =>
                $q->whereBetween('date', [$from, $to])
            );
        }
    }
    // 'all' = no date filter

    $items = $query->get()
        ->filter(function($item) {
            $pp = $item->purchase_price ?? $item->product->purchase_price ?? 0;
            return $item->rate > 0 && $item->rate < $pp;
        })
        ->groupBy(fn($item) => $item->sale->customer_id ?? 'walkin');

    $result = [];
    foreach ($items as $customerId => $groupItems) {
        $customer = $groupItems->first()->sale->customer;
        $totalLoss = $groupItems->sum(function($item) {
            $pp = $item->purchase_price ?? $item->product->purchase_price ?? 0;
            return ($pp - $item->rate) * $item->qty;
        });

        $sales = [];
        foreach ($groupItems as $item) {
            $pp = $item->purchase_price ?? $item->product->purchase_price ?? 0;
            $lossPerUnit = $pp - $item->rate;
            $sales[] = [
                'sale_id'        => $item->sale->id,
                'memo_no'        => $item->sale->memo_no,
                'date'           => \Carbon\Carbon::parse($item->sale->date)->format('d M Y'),
                'product'        => $item->product->name ?? '—',
                'qty'            => $item->qty,
                'purchase_price' => round($pp),
                'sale_price'     => round($item->rate),
                'loss_per_unit'  => round($lossPerUnit),
                'total_loss'     => round($lossPerUnit * $item->qty),
            ];
        }

        $result[] = [
            'customer'   => $customer->name ?? 'Walk-in Customer',
            'total_loss' => round($totalLoss),
            'sales'      => $sales,
        ];
    }

    return response()->json(['items' => $result]);
}
}
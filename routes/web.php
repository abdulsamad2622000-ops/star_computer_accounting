<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\BusinessSettingController;
use App\Exports\ReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

Route::get('/', function () {
    return redirect()->route('login');
});

require __DIR__.'/auth.php';

Route::middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');

    // ── Customers ──────────────────────────────
    Route::middleware('staff.permission:customer_access')->group(function () {
        Route::resource('customers', CustomerController::class);
        Route::delete('customers/{customer}/ledger', [CustomerController::class, 'deleteLedger'])->name('customers.ledger.delete');
        Route::get('customers/{customer}/ledger/pdf', [CustomerController::class, 'ledgerPdf'])->name('customers.ledger.pdf');
        Route::get('customers/{customer}/ledger/excel', [CustomerController::class, 'ledgerExcel'])->name('customers.ledger.excel');
    });
    Route::middleware('staff.permission:customer_payment')->group(function () {
        Route::post('customers/{customer}/payment', [CustomerController::class, 'receivePayment'])->name('customers.payment.store');
        Route::put('customer-payments/{payment}', [CustomerController::class, 'updatePayment'])->name('customers.payment.update');
        Route::patch('customer-payments/{payment}/reschedule', [CustomerController::class, 'reschedulePayment'])->name('customers.payment.reschedule');
        Route::delete('customer-payments/{payment}', [CustomerController::class, 'deletePayment'])->name('customers.payment.delete');
    });
    // Settle Against Vendor
    Route::post('customers/{customer}/settle-vendor', [CustomerController::class, 'settleAgainstVendor'])->name('customers.settle.vendor');

    // ── Vendors ────────────────────────────────
    Route::middleware('staff.permission:vendor_access')->group(function () {
        Route::resource('vendors', VendorController::class);
        Route::delete('vendors/{vendor}/ledger', [VendorController::class, 'deleteLedger'])->name('vendors.ledger.delete');
        Route::get('vendors/{vendor}/ledger/pdf', [VendorController::class, 'ledgerPdf'])->name('vendors.ledger.pdf');
        Route::get('vendors/{vendor}/ledger/excel', [VendorController::class, 'ledgerExcel'])->name('vendors.ledger.excel');
    });
    Route::middleware('staff.permission:vendor_payment')->group(function () {
        Route::post('vendors/{vendor}/payment', [VendorController::class, 'receivePayment'])->name('vendors.payment.store');
        Route::put('vendor-payments/{payment}', [VendorController::class, 'updatePayment'])->name('vendors.payment.update');
        Route::patch('vendor-payments/{payment}/reschedule', [VendorController::class, 'reschedulePayment'])->name('vendors.payment.reschedule');
        Route::delete('vendor-payments/{payment}', [VendorController::class, 'deletePayment'])->name('vendors.payment.delete');
    });
    // Settle Against Customer
    Route::post('vendors/{vendor}/settle-customer', [VendorController::class, 'settleAgainstCustomer'])->name('vendors.settle.customer');

    // ── Inventory ──────────────────────────────
    Route::middleware('staff.permission:inventory_access')->group(function () {
        Route::get('products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
        Route::get('products/export/pdf', [ProductController::class, 'exportPdf'])->name('products.export.pdf');
        Route::get('products/search', [ProductController::class, 'search'])->name('products.search');
        Route::resource('products', ProductController::class);
    });
    Route::post('products/opening/store', [ProductController::class, 'openingStore'])->name('products.opening.store')->middleware('staff.permission:inventory_add_stock');
    Route::post('products/{product}/adjust-qty', [ProductController::class, 'adjustQty'])->name('products.adjust.qty');

    // ── Sale Point ─────────────────────────────
    Route::middleware('staff.permission:sale_access')->group(function () {
        Route::get('sales/pos', [SaleController::class, 'pos'])->name('sales.pos');
        Route::post('sales', [SaleController::class, 'store'])->name('sales.store');
    });
    Route::get('sales/history', [SaleController::class, 'history'])->name('sales.history');
    Route::get('sales/{sale}/invoice', [SaleController::class, 'invoice'])->name('sales.invoice');
    Route::get('sales/{sale}/invoice/pdf', [SaleController::class, 'invoicePdf'])->name('sales.invoice.pdf');
    Route::get('sales/{sale}/return-items', [ReturnController::class, 'getSaleItems'])->name('sales.return.items');
    Route::post('sales/{sale}/return', [ReturnController::class, 'saleReturn'])->name('sales.return.store');
    Route::get('sales/{sale}/edit-data', [SaleController::class, 'editData'])->name('sales.edit.data');
    Route::put('sales/{sale}/update', [SaleController::class, 'updateSale'])->name('sales.update');
    Route::post('sales/{sale}/transfer', [SaleController::class, 'transferSale'])->name('sales.transfer');

    // ── Purchase Point ─────────────────────────
    Route::middleware('staff.permission:purchase_access')->group(function () {
        Route::get('purchases/pos', [SaleController::class, 'purchasePos'])->name('purchases.pos');
        Route::post('purchases', [SaleController::class, 'purchaseStore'])->name('purchases.store');
    });
    Route::get('purchases/history', [SaleController::class, 'purchaseHistory'])->name('purchases.history');
    Route::get('purchases/{sale}/invoice', [SaleController::class, 'purchaseInvoice'])->name('purchases.invoice');
    Route::get('purchases/{sale}/invoice/pdf', [SaleController::class, 'purchaseInvoicePdf'])->name('purchases.invoice.pdf');
    Route::get('purchases/{sale}/items', [SaleController::class, 'getPurchaseItems'])->name('purchases.items');
    Route::post('purchases/{sale}/update-rates', [SaleController::class, 'updatePurchaseRates'])->name('purchases.update.rates');
    Route::get('purchases/{sale}/return-items', [ReturnController::class, 'getSaleItems'])->name('purchases.return.items');
    Route::post('purchases/{sale}/return', [ReturnController::class, 'purchaseReturn'])->name('purchases.return.store');
    Route::get('purchases/{sale}/edit-data', [SaleController::class, 'editPurchaseData'])->name('purchases.edit.data');
    Route::put('purchases/{sale}/update', [SaleController::class, 'updatePurchase'])->name('purchases.update');
    Route::post('purchases/{sale}/transfer', [SaleController::class, 'transferPurchase'])->name('purchases.transfer');

    // ── Daily Report ───────────────────────────
    Route::middleware('staff.permission:report_access')->group(function () {
        Route::get('reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
        Route::put('reports/{sale}', [ReportController::class, 'update'])->name('reports.update');
        Route::delete('reports/{sale}', [ReportController::class, 'destroy'])->name('reports.destroy');
        Route::get('reports/export/excel', function (Request $request) {
            $from = $request->from ?? today()->format('Y-m-d');
            $to   = $request->to   ?? today()->format('Y-m-d');
            return Excel::download(new ReportExport($from, $to, $request->name), 'report-'.$from.'-to-'.$to.'.xlsx');
        })->name('reports.export.excel');
        Route::get('reports/export/pdf', function (Request $request) {
            $from  = $request->from ?? today()->format('Y-m-d');
            $to    = $request->to   ?? today()->format('Y-m-d');
            $sales = \App\Models\Sale::where('type', 'sale')->whereBetween('date', [$from, $to])->with(['customer', 'items.product', 'salesperson'])->get();
            $pdf = Pdf::loadView('reports.pdf', compact('sales', 'from', 'to'));
            return $pdf->download('report-'.$from.'-to-'.$to.'.pdf');
        })->name('reports.export.pdf');
    });

    // ── Business Settings ──────────────────────
    Route::get('settings/business', [BusinessSettingController::class, 'edit'])->name('settings.business');
    Route::put('settings/business', [BusinessSettingController::class, 'update'])->name('settings.business.update');

    // ── Admin Only ─────────────────────────────
    Route::middleware('admin')->group(function () {
        Route::resource('staff', StaffController::class);
        Route::get('settings/permissions', [App\Http\Controllers\StaffPermissionController::class, 'edit'])->name('settings.permissions');
        Route::put('settings/permissions', [App\Http\Controllers\StaffPermissionController::class, 'update'])->name('settings.permissions.update');
    });

    // ── Customer / Vendor PDF Export ───────────
    Route::get('customers/export/pdf', function () {
        $customers = \App\Models\Customer::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('customers.pdf', compact('customers'));
        return $pdf->download('Customers.pdf');
    })->name('customers.export.pdf')->middleware('staff.permission:customer_access');

    Route::get('vendors/export/pdf', function () {
        $vendors = \App\Models\Vendor::all();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('vendors.pdf', compact('vendors'));
        return $pdf->download('Vendors.pdf');
    })->name('vendors.export.pdf')->middleware('staff.permission:vendor_access');

    // WhatsApp Test
    Route::get('test-whatsapp/{phone}', function ($phone) {
        $whatsapp = new \App\Services\WhatsAppService();
        $result = $whatsapp->sendText($phone, "Test message from Star Computer!");
        return $result ? "WhatsApp sent!" : "Failed!";
    });

}); // auth group end
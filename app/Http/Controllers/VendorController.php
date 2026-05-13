<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\Sale;
use App\Models\VendorPayment;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors      = Vendor::latest()->get();
        $totalPayable = Vendor::sum('balance');
        $settledCount = Vendor::where('balance', 0)->count();

        return view('vendors.index', compact(
            'vendors', 'totalPayable', 'settledCount'
        ));
    }

    public function create()
    {
        return view('vendors.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);

        Vendor::create([
            'name'            => $request->name,
            'contact1'        => $request->contact1,
            'contact2'        => $request->contact2,
            'address'         => $request->address,
            'cnic'            => $request->cnic,
            'opening_balance' => $request->opening_balance ?? 0,
            'balance'         => $request->opening_balance ?? 0,
        ]);

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor add ho gaya!');
    }

  public function show(Vendor $vendor)
{
    $purchases = Sale::where('vendor_id', $vendor->id)
        ->where('type', 'purchase')
        ->with('items.product')
        ->latest()
        ->get();

    $payments = VendorPayment::where('vendor_id', $vendor->id)
        ->latest()
        ->get();

    $totalBill   = $purchases->sum('total');
    $totalPaid   = $purchases->sum('paid') + $payments->sum('amount');
    $balance     = $vendor->balance;
    $cashTotal   = $payments->where('method', 'cash')->sum('amount');
    $onlineTotal = $payments->where('method', 'online')->sum('amount');
    $chequeTotal = $payments->where('method', 'cheque')->sum('amount');

    // Loss calculation — items jo purchase price se kam mein biche
    $totalLoss = 0;
    foreach ($purchases as $purchase) {
        foreach ($purchase->items as $item) {
            $purchasePrice = $item->rate;
            $salePrice     = $item->product->sale_price ?? 0;
            if ($salePrice > 0 && $salePrice < $purchasePrice) {
                $totalLoss += ($purchasePrice - $salePrice) * $item->qty;
            }
        }
    }

    return view('vendors.show', compact(
        'vendor', 'purchases', 'payments',
        'totalBill', 'totalPaid', 'balance',
        'cashTotal', 'onlineTotal', 'chequeTotal',
        'totalLoss'
    ));
}
    public function edit(Vendor $vendor)
    {
        return view('vendors.edit', compact('vendor'));
    }

    public function update(Request $request, Vendor $vendor)
    {
        $request->validate(['name' => 'required']);
        $vendor->update($request->all());

        return redirect()->route('vendors.index')
            ->with('success', 'Vendor update ho gaya!');
    }

    public function destroy(Vendor $vendor)
    {
        $vendor->delete();
        return redirect()->route('vendors.index')
            ->with('success', 'Vendor delete ho gaya!');
    }

    public function receivePayment(Request $request, Vendor $vendor)
    {
        $rules = [
            'date'   => 'required|date',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,online,cheque',
        ];

     if ($request->method === 'online') {
    $rules['platform']       = 'nullable';
    $rules['account_number'] = 'nullable';
    $rules['account_title']  = 'nullable';
}

        if ($request->method === 'cheque') {
            $rules['cheque_no']   = 'required';
            $rules['cheque_date'] = 'required|date';
            $rules['bank_name']   = 'required';
        }

        $request->validate($rules);

        VendorPayment::create([
            'vendor_id'      => $vendor->id,
            'date'           => $request->date,
            'amount'         => $request->amount,
            'method'         => $request->method,
            'platform'       => $request->platform,
            'account_number' => $request->account_number,
            'account_title'  => $request->account_title,
            'cheque_no'      => $request->cheque_no,
            'cheque_date'    => $request->cheque_date,
            'bank_name'      => $request->bank_name,
            'note'           => $request->note,
        ]);

        $vendor->decrement('balance', $request->amount);

        return back()->with('success', '✅ Payment send ho gaya!');
    }

    public function updatePayment(Request $request, VendorPayment $payment)
    {
        $rules = [
            'date'   => 'required|date',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|in:cash,online,cheque',
        ];

    if ($request->method === 'online') {
    $rules['platform']       = 'nullable';
    $rules['account_number'] = 'nullable';
    $rules['account_title']  = 'nullable';
}

        if ($request->method === 'cheque') {
            $rules['cheque_no']   = 'required';
            $rules['cheque_date'] = 'required|date';
            $rules['bank_name']   = 'required';
        }

        $request->validate($rules);

        $oldAmount = $payment->amount;
        $newAmount = $request->amount;
        $diff      = $newAmount - $oldAmount;
        $payment->vendor->increment('balance', $diff);

        $payment->update([
            'date'           => $request->date,
            'amount'         => $request->amount,
            'method'         => $request->method,
            'platform'       => $request->platform,
            'account_number' => $request->account_number,
            'account_title'  => $request->account_title,
            'cheque_no'      => $request->cheque_no,
            'cheque_date'    => $request->cheque_date,
            'bank_name'      => $request->bank_name,
            'note'           => $request->note,
        ]);

        return back()->with('success', '✅ Payment update ho gaya!');
    }

    public function reschedulePayment(Request $request, VendorPayment $payment)
    {
        $request->validate(['cheque_date' => 'required|date']);
        $payment->update(['cheque_date' => $request->cheque_date]);

        return back()->with('success', '✅ Cheque date update ho gaya!');
    }

    public function deletePayment(VendorPayment $payment)
    {
        $payment->vendor->increment('balance', $payment->amount);
        $payment->delete();

        return back()->with('success', '✅ Payment delete ho gaya!');
    }

   // ✅ VendorController.php — deleteLedger() replace karo is se

public function deleteLedger(Vendor $vendor)
{
    $purchases = Sale::where('vendor_id', $vendor->id)
        ->where('type', 'purchase')
        ->with('items.product')
        ->get();

    foreach ($purchases as $purchase) {
        // Inventory wapas karo
        foreach ($purchase->items as $item) {
            if ($item->product) {
                $item->product->decrement('remaining_qty', $item->qty);
                $item->product->decrement('received_qty', $item->qty);
            }
        }

        // Purchase items permanently delete karo
        $purchase->items()->forceDelete();

        // StockReturns bhi delete karo (agar hain)
        $purchase->returns()->forceDelete();

        // Purchase permanently delete karo
        $purchase->forceDelete();
    }

    // Vendor payments permanently delete karo
    VendorPayment::where('vendor_id', $vendor->id)->forceDelete();

    // Balance reset karo
    $vendor->update(['balance' => $vendor->opening_balance ?? 0]);

    return redirect()->route('vendors.show', $vendor)
        ->with('success', '✅ Ledger permanently delete ho gaya!');
}

    public function ledgerPdf(Vendor $vendor, Request $request)
    {
        $purchases = Sale::where('vendor_id', $vendor->id)
            ->where('type', 'purchase')
            ->with('items.product')
            ->latest()
            ->get();

        $payments      = VendorPayment::where('vendor_id', $vendor->id)
            ->latest()->get();
        $totalBill     = $purchases->sum('total');
        $totalPaid     = $purchases->sum('paid') + $payments->sum('amount');
        $balance       = $vendor->balance;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'vendors.ledger-pdf',
            compact('vendor', 'purchases', 'payments',
                    'totalBill', 'totalPaid', 'balance')
        );

        return $pdf->download("Ledger-{$vendor->name}.pdf");
    }


    public function ledgerExcel(Vendor $vendor)
{
    return \Maatwebsite\Excel\Facades\Excel::download(
        new \App\Exports\VendorLedgerExport($vendor),
        "Ledger-{$vendor->name}.xlsx"
    );
}
}
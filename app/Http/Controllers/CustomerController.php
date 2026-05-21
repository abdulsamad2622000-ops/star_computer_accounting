<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Sale;
use App\Models\Vendor;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
   public function index()
{
    $customers       = Customer::latest()->get();
    $totalReceivable = Customer::sum('balance');
    $settledCount    = Customer::where('balance', 0)->count();

    $customersJson = $customers->map(function($c) {
        return [
            'name'    => $c->name,
            'contact' => $c->contact1 ?? '—',
            'balance' => $c->balance,
            'status'  => $c->balance > 0 ? 'receivable' : 'settled',
            'url'     => route('customers.show', $c),
        ];
    });

    return view('customers.index', compact(
        'customers', 'totalReceivable', 'settledCount', 'customersJson'
    ));
}
    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);

        Customer::create([
            'name'            => $request->name,
            'contact1'        => $request->contact1,
            'contact2'        => $request->contact2,
            'address'         => $request->address,
            'cnic'            => $request->cnic,
            'opening_balance' => $request->opening_balance ?? 0,
            'balance'         => $request->opening_balance ?? 0,
        ]);

        return redirect()->route('customers.index')
            ->with('success', 'Customer add ho gaya!');
    }

  public function show(Customer $customer)
{
    $sales = Sale::where('customer_id', $customer->id)
        ->where('type', 'sale')
        ->with('items.product')
        ->latest()
        ->get();

    $payments = CustomerPayment::where('customer_id', $customer->id)
        ->latest()
        ->get();

    $totalBill     = $sales->sum('total');
    $totalReceived = $sales->sum('paid') + $payments->sum('amount');
    $totalPaid     = $totalReceived;
    $balance       = $customer->balance;
    $cashTotal     = $payments->where('method', 'cash')->sum('amount');
    $onlineTotal   = $payments->where('method', 'online')->sum('amount');
    $chequeTotal   = $payments->where('method', 'cheque')->sum('amount');

    // Loss calculation
    $totalLoss = 0;
    foreach ($sales as $sale) {
        foreach ($sale->items as $item) {
            $purchasePrice = $item->product->purchase_price ?? 0;
            $salePrice     = $item->rate;
            if ($salePrice < $purchasePrice) {
                $totalLoss += ($purchasePrice - $salePrice) * $item->qty;
            }
        }
    }

    return view('customers.show', compact(
        'customer', 'sales', 'payments',
        'totalBill', 'totalPaid', 'totalReceived', 'balance',
        'cashTotal', 'onlineTotal', 'chequeTotal', 'totalLoss'
    ));
}

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate(['name' => 'required']);
        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'Customer update ho gaya!');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();
        return redirect()->route('customers.index')
            ->with('success', 'Customer delete ho gaya!');
    }

    public function receivePayment(Request $request, Customer $customer)
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

        CustomerPayment::create([
            'customer_id'    => $customer->id,
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

        $customer->decrement('balance', $request->amount);

        return back()->with('success', '✅ Payment receive ho gaya!');
    }

    public function updatePayment(Request $request, CustomerPayment $payment)
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
        $payment->customer->increment('balance', $diff);

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

    public function reschedulePayment(Request $request, CustomerPayment $payment)
    {
        $request->validate(['cheque_date' => 'required|date']);
        $payment->update(['cheque_date' => $request->cheque_date]);

        return back()->with('success', '✅ Cheque date update ho gaya!');
    }

    public function deletePayment(CustomerPayment $payment)
    {
        $payment->customer->increment('balance', $payment->amount);
        $payment->delete();

        return back()->with('success', '✅ Payment delete ho gaya!');
    }

  // ✅ CustomerController.php — deleteLedger() replace karo is se
public function deleteLedger(Customer $customer)
{
    $sales = Sale::where('customer_id', $customer->id)
        ->where('type', 'sale')
        ->with('items.product')
        ->get();

    foreach ($sales as $sale) {
        foreach ($sale->items as $item) {
            if ($item->product) {
                $item->product->increment('remaining_qty', $item->qty);
                $item->product->decrement('sold_qty', $item->qty);
            }
        }
        $sale->items()->delete();
        $sale->delete();
    }

    CustomerPayment::where('customer_id', $customer->id)->delete();
    $customer->update(['balance' => $customer->opening_balance ?? 0]);

    return redirect()->route('customers.show', $customer)
        ->with('success', '✅ Ledger permanently delete ho gaya!');
}



    public function ledgerPdf(Customer $customer)
    {
        $sales = Sale::where('customer_id', $customer->id)
            ->where('type', 'sale')
            ->with('items.product')
            ->latest()
            ->get();

        $payments    = CustomerPayment::where('customer_id', $customer->id)
            ->latest()->get();
        $totalBill   = $sales->sum('total');
        $totalPaid   = $sales->sum('paid') + $payments->sum('amount');
        $balance     = $customer->balance;

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'customers.ledger-pdf',
            compact('customer', 'sales', 'payments',
                    'totalBill', 'totalPaid', 'balance')
        );

        return $pdf->download("Ledger-{$customer->name}.pdf");
    }

    public function ledgerExcel(Customer $customer)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\CustomerLedgerExport($customer),
            "Ledger-{$customer->name}.xlsx"
        );
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function daily(Request $request)
    {
        $from = $request->from ?? today()->format('Y-m-d');
        $to   = $request->to   ?? today()->format('Y-m-d');

        $sales = Sale::where('type', 'sale')
            ->whereBetween('date', [$from, $to])
            ->with(['customer', 'items.product', 'salesperson'])
            ->when($request->name, fn($q) => 
                $q->whereHas('customer', fn($q) => 
                    $q->where('name', 'like', '%'.$request->name.'%')
                )
            )
            ->latest()
            ->get();

        $totalBill     = $sales->sum('total');
        $totalReceived = $sales->sum('paid');
        $totalBalance  = $sales->sum('balance');

        return view('reports.daily', compact(
            'sales',
            'from',
            'to',
            'totalBill',
            'totalReceived',
            'totalBalance'
        ));
    }

    public function update(Request $request, Sale $sale)
    {
        $sale->update($request->all());
        return redirect()->back()->with('success', 'Transaction update ho gaya!');
    }

    public function destroy(Sale $sale)
    {
        // Stock wapas karo
        foreach ($sale->items as $item) {
            $item->product->increment('remaining_qty', $item->qty);
            $item->product->decrement('sold_qty', $item->qty);
        }

        // Customer balance update
        if ($sale->customer_id) {
            $sale->customer->decrement('balance', $sale->balance);
        }

        $sale->delete();

        return redirect()->back()->with('success', 'Transaction delete ho gaya!');
    }
}
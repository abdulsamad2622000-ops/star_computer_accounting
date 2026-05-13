<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\StockReturn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnController extends Controller
{
    public function getSaleItems(Sale $sale)
    {
        $sale->load('items.product');
        $returns = StockReturn::where('sale_id', $sale->id)->get();

        $items = $sale->items->map(function($item) use ($returns) {
            $returnedQty = $returns
                ->where('sale_item_id', $item->id)
                ->sum('qty');
            $item->returned_qty   = $returnedQty;
            $item->returnable_qty = $item->qty - $returnedQty;
            return $item;
        })->filter(fn($item) => $item->returnable_qty > 0);

        return response()->json([
            'sale'  => $sale,
            'items' => $items->values(),
        ]);
    }

    public function saleReturn(Request $request, Sale $sale)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|exists:sale_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'reason'      => 'nullable|string',
            'date'        => 'required|date',
        ]);

        DB::transaction(function() use ($request, $sale) {
            $totalReturn = 0;

            foreach ($request->items as $item) {
                $saleItem = SaleItem::find($item['id']);
                $qty      = (int) $item['qty'];

                $alreadyReturned = StockReturn::where('sale_item_id', $saleItem->id)
                    ->sum('qty');
                $returnable = $saleItem->qty - $alreadyReturned;

                if ($qty > $returnable) {
                    throw new \Exception(
                        "⚠️ {$saleItem->product->name} ki max returnable qty: {$returnable}"
                    );
                }

                $returnTotal  = $qty * $saleItem->rate;
                $totalReturn += $returnTotal;

                StockReturn::create([
                    'sale_id'      => $sale->id,
                    'product_id'   => $saleItem->product_id,
                    'sale_item_id' => $saleItem->id,
                    'type'         => 'sale_return',
                    'qty'          => $qty,
                    'rate'         => $saleItem->rate,
                    'total'        => $returnTotal,
                    'reason'       => $request->reason,
                    'date'         => $request->date,
                    'user_id'      => auth()->id(),
                ]);

                $saleItem->product->increment('remaining_qty', $qty);
                $saleItem->product->decrement('sold_qty', $qty);
            }

            if ($sale->customer && $totalReturn > 0) {
                $sale->customer->decrement('balance', $totalReturn);
            }
        });

        return response()->json([
            'success' => true,
            'message' => '✅ Sale return process ho gaya!',
        ]);
    }

    public function purchaseReturn(Request $request, Sale $sale)
    {
        $request->validate([
            'items'       => 'required|array|min:1',
            'items.*.id'  => 'required|exists:sale_items,id',
            'items.*.qty' => 'required|integer|min:1',
            'reason'      => 'nullable|string',
            'date'        => 'required|date',
        ]);

        DB::transaction(function() use ($request, $sale) {
            $totalReturn = 0;

            foreach ($request->items as $item) {
                $saleItem = SaleItem::find($item['id']);
                $qty      = (int) $item['qty'];

                $alreadyReturned = StockReturn::where('sale_item_id', $saleItem->id)
                    ->sum('qty');
                $returnable = $saleItem->qty - $alreadyReturned;

                if ($qty > $returnable) {
                    throw new \Exception(
                        "⚠️ {$saleItem->product->name} ki max returnable qty: {$returnable}"
                    );
                }

                $returnTotal  = $qty * $saleItem->rate;
                $totalReturn += $returnTotal;

                StockReturn::create([
                    'sale_id'      => $sale->id,
                    'product_id'   => $saleItem->product_id,
                    'sale_item_id' => $saleItem->id,
                    'type'         => 'purchase_return',
                    'qty'          => $qty,
                    'rate'         => $saleItem->rate,
                    'total'        => $returnTotal,
                    'reason'       => $request->reason,
                    'date'         => $request->date,
                    'user_id'      => auth()->id(),
                ]);

                $saleItem->product->decrement('remaining_qty', $qty);
                $saleItem->product->decrement('received_qty', $qty);
            }

            if ($sale->vendor && $totalReturn > 0) {
                $sale->vendor->decrement('balance', $totalReturn);
            }
        });

        return response()->json([
            'success' => true,
            'message' => '✅ Purchase return process ho gaya!',
        ]);
    }
}
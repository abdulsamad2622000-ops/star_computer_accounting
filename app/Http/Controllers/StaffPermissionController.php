<?php

namespace App\Http\Controllers;

use App\Models\StaffPermission;
use App\Models\User;
use Illuminate\Http\Request;

class StaffPermissionController extends Controller
{
    public function edit(Request $request)
    {
        $staffList  = User::where('role', 'staff')->orderBy('name')->get();
        $selectedId = $request->user_id ?? $staffList->first()?->id;
        $permissions = null;

        if ($selectedId) {
            $permissions = StaffPermission::firstOrCreate(
                ['user_id' => $selectedId],
                [
                    'sale_access'      => true,
                    'sale_history'     => true,
                    'sale_return'      => true,
                    'purchase_access'  => true,
                    'purchase_history' => true,
                    'purchase_return'  => true,
                ]
            );
        }

        return view('settings.permissions', compact(
            'staffList', 'selectedId', 'permissions'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $permissions = StaffPermission::firstOrCreate(
            ['user_id' => $request->user_id]
        );

        $permissions->update([
            'sale_access'           => $request->has('sale_access'),
            'sale_history'          => $request->has('sale_history'),
            'sale_return'           => $request->has('sale_return'),
            'purchase_access'       => $request->has('purchase_access'),
            'purchase_history'      => $request->has('purchase_history'),
            'purchase_return'       => $request->has('purchase_return'),
            'purchase_price'        => $request->has('purchase_price'),
            'purchase_rate_edit'    => $request->has('purchase_rate_edit'),
            'customer_access'       => $request->has('customer_access'),
            'customer_ledger'       => $request->has('customer_ledger'),
            'customer_payment'      => $request->has('customer_payment'),
            'vendor_access'         => $request->has('vendor_access'),
            'vendor_ledger'         => $request->has('vendor_ledger'),
            'vendor_payment'        => $request->has('vendor_payment'),
            'inventory_access'      => $request->has('inventory_access'),
            'inventory_prices'      => $request->has('inventory_prices'),
            'inventory_stock_value' => $request->has('inventory_stock_value'),
            'inventory_edit'        => $request->has('inventory_edit'),
            'inventory_add_stock'   => $request->has('inventory_add_stock'),
            'report_access'         => $request->has('report_access'),
            'dashboard_access'      => $request->has('dashboard_access'),
        ]);

        return redirect()
            ->route('settings.permissions', ['user_id' => $request->user_id])
            ->with('success', '✅ Permissions update ho gayi!');
    }
}
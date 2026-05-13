<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffPermission extends Model
{
    protected $fillable = [
        'user_id',
        'sale_access',
        'sale_history',
        'sale_return',
        'purchase_access',
        'purchase_history',
        'purchase_return',
        'purchase_price',
        'purchase_rate_edit',
        'customer_access',
        'customer_ledger',
        'customer_payment',
        'vendor_access',
        'vendor_ledger',
        'vendor_payment',
        'inventory_access',
        'inventory_prices',
        'inventory_stock_value',
        'inventory_edit',
        'inventory_add_stock',
        'report_access',
        'dashboard_access',
    ];

    protected $casts = [
        'sale_access'           => 'boolean',
        'sale_history'          => 'boolean',
        'sale_return'           => 'boolean',
        'purchase_access'       => 'boolean',
        'purchase_history'      => 'boolean',
        'purchase_return'       => 'boolean',
        'purchase_price'        => 'boolean',
        'purchase_rate_edit'    => 'boolean',
        'customer_access'       => 'boolean',
        'customer_ledger'       => 'boolean',
        'customer_payment'      => 'boolean',
        'vendor_access'         => 'boolean',
        'vendor_ledger'         => 'boolean',
        'vendor_payment'        => 'boolean',
        'inventory_access'      => 'boolean',
        'inventory_prices'      => 'boolean',
        'inventory_stock_value' => 'boolean',
        'inventory_edit'        => 'boolean',
        'inventory_add_stock'   => 'boolean',
        'report_access'         => 'boolean',
        'dashboard_access'      => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
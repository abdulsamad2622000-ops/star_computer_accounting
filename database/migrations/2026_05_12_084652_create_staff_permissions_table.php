<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_permissions', function (Blueprint $table) {
            $table->id();
            $table->boolean('sale_access')->default(true);
            $table->boolean('sale_history')->default(true);
            $table->boolean('sale_return')->default(true);
            $table->boolean('purchase_access')->default(true);
            $table->boolean('purchase_history')->default(true);
            $table->boolean('purchase_return')->default(true);
            $table->boolean('purchase_price')->default(false);
            $table->boolean('purchase_rate_edit')->default(false);
            $table->boolean('customer_access')->default(false);
            $table->boolean('customer_ledger')->default(false);
            $table->boolean('customer_payment')->default(false);
            $table->boolean('vendor_access')->default(false);
            $table->boolean('vendor_ledger')->default(false);
            $table->boolean('vendor_payment')->default(false);
            $table->boolean('inventory_access')->default(false);
            $table->boolean('inventory_prices')->default(false);
            $table->boolean('inventory_edit')->default(false);
            $table->boolean('inventory_add_stock')->default(false);
            $table->boolean('report_access')->default(false);
            $table->boolean('dashboard_access')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_permissions');
    }
};
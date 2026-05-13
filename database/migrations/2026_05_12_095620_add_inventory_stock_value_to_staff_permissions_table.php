<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('staff_permissions', 'inventory_stock_value')) {
            Schema::table('staff_permissions', function (Blueprint $table) {
                $table->boolean('inventory_stock_value')->default(false)->after('inventory_prices');
            });
        }
    }

    public function down(): void
    {
        Schema::table('staff_permissions', function (Blueprint $table) {
            $table->dropColumn('inventory_stock_value');
        });
    }
};
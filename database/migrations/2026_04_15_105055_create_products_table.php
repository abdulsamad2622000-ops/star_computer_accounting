<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('stock_code')->unique()->nullable();
            $table->string('name');
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('purchase_price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->integer('received_qty')->default(0);
            $table->integer('sold_qty')->default(0);
            $table->integer('remaining_qty')->default(0);
            $table->integer('alert_qty')->default(5);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
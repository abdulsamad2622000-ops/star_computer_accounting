<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('memo_no')->unique();
            $table->enum('type', ['sale', 'purchase']);
            $table->foreignId('customer_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->constrained();
            $table->date('date');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2)->default(0);
            $table->decimal('paid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->enum('payment_type', ['cash', 'credit', 'partial'])->default('cash');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->decimal('amount', 12, 2);
            $table->enum('method', ['cash', 'online', 'cheque']);
            // Online fields
            $table->string('platform')->nullable();
            $table->string('account_number')->nullable();
            $table->string('account_title')->nullable();
            // Cheque fields
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name')->nullable();
            // Common
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_payments');
    }
};
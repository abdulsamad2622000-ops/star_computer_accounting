<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('vendor_payments')) {
            Schema::create('vendor_payments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('vendor_id')
                      ->constrained()
                      ->onDelete('cascade');
                $table->date('date');
                $table->decimal('amount', 12, 2);
                $table->enum('method', ['cash', 'online', 'cheque']);
                $table->string('platform')->nullable();
                $table->string('account_number')->nullable();
                $table->string('account_title')->nullable();
                $table->string('cheque_no')->nullable();
                $table->date('cheque_date')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_payments');
    }
};
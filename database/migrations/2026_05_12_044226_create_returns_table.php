<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_item_id')->constrained('sale_items')->onDelete('cascade');
            $table->enum('type', ['sale_return', 'purchase_return']);
            $table->integer('qty');
            $table->decimal('rate', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('reason')->nullable();
            $table->date('date');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
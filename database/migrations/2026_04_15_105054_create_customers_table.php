<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact1')->nullable();
            $table->string('contact2')->nullable();
            $table->string('address')->nullable();
            $table->string('cnic')->nullable();
            $table->decimal('opening_balance', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
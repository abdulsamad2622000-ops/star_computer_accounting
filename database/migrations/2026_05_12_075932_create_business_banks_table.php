<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('business_banks', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('account_title');
            $table->string('account_number');
            $table->string('iban')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('business_banks');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('business_settings', function (Blueprint $table) {
            $table->id();
            $table->string('business_name')->default('Star Computer');
            $table->string('tagline')->nullable();
            $table->string('address')->nullable();

            // Contacts
            $table->string('contact1_name')->nullable();
            $table->string('contact1_phone')->nullable();
            $table->string('contact2_name')->nullable();
            $table->string('contact2_phone')->nullable();
            $table->string('contact3_name')->nullable();
            $table->string('contact3_phone')->nullable();

            // Bank
            $table->string('bank_name')->nullable();
            $table->string('bank_account_title')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_iban')->nullable();

            // Notes
            $table->text('notes')->nullable();

            // Footer
            $table->string('thank_you_message')
                  ->default('Thank you for your business.');

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('business_settings');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // customer_payments table
        DB::statement("ALTER TABLE customer_payments MODIFY COLUMN method ENUM('cash','online','cheque','adjustment')");

        // vendor_payments table
        DB::statement("ALTER TABLE vendor_payments MODIFY COLUMN method ENUM('cash','online','cheque','adjustment')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE customer_payments MODIFY COLUMN method ENUM('cash','online','cheque')");
        DB::statement("ALTER TABLE vendor_payments MODIFY COLUMN method ENUM('cash','online','cheque')");
    }
};
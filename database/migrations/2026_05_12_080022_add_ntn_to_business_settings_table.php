<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    if (!Schema::hasColumn('business_settings', 'ntn')) {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->string('ntn')->nullable()->after('bank_iban');
        });
    }
}

    public function down(): void
    {
        Schema::table('business_settings', function (Blueprint $table) {
            $table->dropColumn('ntn');
        });
    }
};
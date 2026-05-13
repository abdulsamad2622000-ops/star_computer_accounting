<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('vendor_payments', function (Blueprint $table) {
            if (!Schema::hasColumn('vendor_payments', 'method')) {
                $table->string('method')->nullable()->after('amount');
            }
            if (!Schema::hasColumn('vendor_payments', 'platform')) {
                $table->string('platform')->nullable()->after('method');
            }
            if (!Schema::hasColumn('vendor_payments', 'account_number')) {
                $table->string('account_number')->nullable()->after('platform');
            }
            if (!Schema::hasColumn('vendor_payments', 'account_title')) {
                $table->string('account_title')->nullable()->after('account_number');
            }
            if (!Schema::hasColumn('vendor_payments', 'cheque_no')) {
                $table->string('cheque_no')->nullable()->after('account_title');
            }
            if (!Schema::hasColumn('vendor_payments', 'cheque_date')) {
                $table->date('cheque_date')->nullable()->after('cheque_no');
            }
            if (!Schema::hasColumn('vendor_payments', 'bank_name')) {
                $table->string('bank_name')->nullable()->after('cheque_date');
            }
            if (!Schema::hasColumn('vendor_payments', 'note')) {
                $table->text('note')->nullable()->after('bank_name');
            }
        });
    }

    public function down()
    {
        Schema::table('vendor_payments', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('vendor_payments', 'method')        ? 'method'         : null,
                Schema::hasColumn('vendor_payments', 'platform')      ? 'platform'       : null,
                Schema::hasColumn('vendor_payments', 'account_number')? 'account_number' : null,
                Schema::hasColumn('vendor_payments', 'account_title') ? 'account_title'  : null,
                Schema::hasColumn('vendor_payments', 'cheque_no')     ? 'cheque_no'      : null,
                Schema::hasColumn('vendor_payments', 'cheque_date')   ? 'cheque_date'    : null,
                Schema::hasColumn('vendor_payments', 'bank_name')     ? 'bank_name'      : null,
                Schema::hasColumn('vendor_payments', 'note')          ? 'note'           : null,
            ]));
        });
    }
};
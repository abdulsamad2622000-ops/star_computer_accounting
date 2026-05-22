<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        $exists = DB::select("
            SHOW INDEX FROM products 
            WHERE Key_name = 'products_name_unique'
        ");

        if (!empty($exists)) {
            Schema::table('products', function (Blueprint $table) {
                $table->dropUnique(['name']);
            });
        }
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->unique('name');
        });
    }
};
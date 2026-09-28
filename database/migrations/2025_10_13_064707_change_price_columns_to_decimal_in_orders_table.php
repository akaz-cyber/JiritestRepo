<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('sub_total', 15, 2)->change();
            $table->decimal('delivery_charge', 15, 2)->default(0)->change();
            $table->decimal('coupon', 15, 2)->nullable()->change();
            $table->decimal('total_amount', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->float('sub_total')->change();
            $table->float('delivery_charge')->default(0)->change();
            $table->float('coupon')->nullable()->change();
            $table->float('total_amount')->change();
        });
    }
};

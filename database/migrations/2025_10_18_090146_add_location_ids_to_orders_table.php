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
            $table->string('province_id')->nullable()->after('phone');
            $table->string('city_id')->nullable()->after('province_id');
            $table->string('province_name')->nullable()->after('province_id');
            $table->string('city_name')->nullable()->after('city_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['province_id', 'city_id']);
            $table->dropColumn(['province_name', 'city_name']);
        });
    }
};

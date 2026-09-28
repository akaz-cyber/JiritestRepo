<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            // tambahkan setelah product_id biar rapi
            $table->unsignedBigInteger('variant_id')->nullable()->after('product_id');

            // FK ke product_variants, hapus ke null kalau varian dihapus
            $table->foreign('variant_id')
                  ->references('id')->on('product_variants')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropForeign(['variant_id']);
            $table->dropColumn('variant_id');
        });
    }
};

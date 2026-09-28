<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            // Kolom 'price' SUDAH ada dan dipakai sebagai Harga Jual.
            // Tambahkan kolom manual-pricing:
            if (!Schema::hasColumn('product_variants', 'original_price')) {
                $table->decimal('original_price', 12, 2)
                      ->nullable()
                      ->after('price');                 // Harga Asli (coret)
            }

            if (!Schema::hasColumn('product_variants', 'discount_percent')) {
                $table->unsignedTinyInteger('discount_percent')
                      ->nullable()
                      ->after('original_price');         // Diskon (%) manual (0–99)
            }
        });
    }

    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            if (Schema::hasColumn('product_variants', 'discount_percent')) {
                $table->dropColumn('discount_percent');
            }
            if (Schema::hasColumn('product_variants', 'original_price')) {
                $table->dropColumn('original_price');
            }
        });
    }
};

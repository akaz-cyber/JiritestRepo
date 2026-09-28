<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')
                  ->constrained('products')
                  ->cascadeOnDelete();

            $table->string('variant_name');               // contoh: "1 Kg", "5 Kg", "Model A"
            $table->decimal('price', 15 )->nullable();  // harga khusus varian (boleh null)
            $table->unsignedInteger('stock')->default(0); // stok per varian
            // opsional
            $table->string('sku')->nullable()->unique();
            $table->decimal('weight', 10)->nullable(); // gram
            $table->string('photo')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->unique(['product_id', 'variant_name']);
            $table->index(['product_id', 'is_active']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};

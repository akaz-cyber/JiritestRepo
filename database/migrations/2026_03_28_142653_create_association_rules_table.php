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
        Schema::create('association_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('antecedent_variant_id')->nullable();
            $table->unsignedBigInteger('consequent_variant_id')->nullable();
            $table->float('support', 8, 4)->nullable();
            $table->float('confidence', 8, 4)->nullable();
            $table->float('lift', 8, 4)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('association_rules');
    }
};

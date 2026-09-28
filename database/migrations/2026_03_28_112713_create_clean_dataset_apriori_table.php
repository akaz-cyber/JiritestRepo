<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('clean_dataset_apriori', function (Blueprint $table) {
            $table->id();
            $table->string('OrderID');
            $table->unsignedBigInteger('product_variant_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('clean_dataset_apriori');
    }
};

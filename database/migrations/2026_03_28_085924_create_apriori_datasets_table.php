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
        Schema::create('apriori_datasets', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->nullable();
            $table->string('index_id')->nullable();
            $table->string('item_description')->nullable();
            $table->integer('harga_satuan')->nullable();
            $table->integer('qty')->nullable();
            $table->integer('harga_total')->nullable();
            $table->integer('berat')->nullable();
            $table->integer('berat_total')->nullable();
            $table->string('lokasi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('apriori_datasets');
    }
};

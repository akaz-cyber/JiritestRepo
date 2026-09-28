<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Opsional: label supaya user bisa kasih nama "Rumah", "Kantor", dll
            $table->string('label')->nullable();

            // Disesuaikan dengan form checkout kamu
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');

            $table->string('province_id');
            $table->string('province_name');
            $table->string('city_id');
            $table->string('city_name');
            $table->string('post_code');
            $table->text('address1'); // alamat detail / jalan

            // Tandai default address
            $table->boolean('is_default')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('addresses');
    }
};
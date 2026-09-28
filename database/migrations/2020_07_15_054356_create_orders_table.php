<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->unsignedBigInteger('user_id')->nullable();

            // Kolom Harga dan Kuantitas
            $table->float('sub_total');
            $table->float('delivery_charge')->default(0);
            $table->float('coupon')->nullable();
            $table->float('total_amount');
            $table->integer('quantity');

            // Kolom Pembayaran & Status
            $table->string('payment_method', 50)->default('cod'); // Diubah menjadi string untuk fleksibilitas
            $table->enum('payment_status',['paid','unpaid'])->default('unpaid');
            $table->enum('status',['new','process','delivered','cancel'])->default('new');

            // Kolom Detail Pengiriman (RajaOngkir)
            $table->string('shipping_courier')->nullable();
            $table->string('shipping_service')->nullable();

            // Kolom Informasi Pelanggan
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email');
            $table->string('phone');
            $table->text('address1');
            $table->string('post_code')->nullable();

            $table->timestamps();

            // Foreign Key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}

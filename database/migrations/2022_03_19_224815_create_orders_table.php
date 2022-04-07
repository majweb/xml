<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
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
            $table->string('filename')->unique();
            $table->string('order_number')->unique();
            $table->dateTime('order_date');
            $table->dateTime('expected_delivery_date');
            $table->string('document_function_code')->nullable();
            $table->string('buyer_iln')->nullable();
            $table->string('seller_iln')->nullable();
            $table->string('delivery_point_iln')->nullable();
            $table->dateTime('date_of_issue');
            $table->dateTime('date_of_return')->nullable();
            $table->dateTime('date_of_invoice')->nullable();
            $table->string('status');
            $table->timestamps();
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
};

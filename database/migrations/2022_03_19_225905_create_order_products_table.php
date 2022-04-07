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
        Schema::create('order_products', function (Blueprint $table) {
            $table->id();
            $table->string('line_number');
            $table->string('ean')->unique();
            $table->string('buyer_item_code');
            $table->string('item_description');
            $table->integer('ordered_quantity');
            $table->integer('ordered_quantity_updated');
            $table->string('unit_of_measure');
            $table->datetime('expected_delivery_date');
            $table->foreignId('order_id')->constrained();
            // $table->string('lp');
            // $table->string('product')->unique();
            // $table->string('jm');
            // $table->integer('quantity');
            // $table->integer('quantityUpdated');
            // $table->foreignId('order_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('standing_order_day_products', function (Blueprint $table) {
            $table->increments('standing_order_day_product_id')->primary();
            $table->unsignedInteger('standing_order_day_id');
            $table->foreign('standing_order_day_id')->references('standing_order_day_id')->on('standing_order_days');
            $table->unsignedInteger('product_id');
            $table->foreign('product_id')->references('product_id')->on('products');
            $table->integer('product_qty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standing_order_day_products');
    }
};

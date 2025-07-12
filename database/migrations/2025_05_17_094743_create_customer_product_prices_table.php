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
        Schema::create('customer_product_prices', function (Blueprint $table) {
            $table->increments('customer_product_price_id')->primary();
            $table->unsignedInteger('customer_id')->nullable();
            $table->unsignedInteger('product_id');
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('set null');
            $table->foreign('product_id')->references('product_id')->on('products');
            $table->decimal('customer_product_price', 10, 4);

            $table->unique(['customer_id', 'product_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_product_prices');
    }
};

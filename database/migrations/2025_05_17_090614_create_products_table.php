<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('product_id')->primary();
            $table->string('product_name', 200);
            $table->decimal('product_unit_price');
            $table->decimal('product_pack_price')->nullable();
            $table->integer('product_weight_g')->nullable();
            $table->integer('product_qty_per_pack')->nullable();
            $table->timestamps();
        });

        DB::update('ALTER TABLE products AUTO_INCREMENT = 500');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

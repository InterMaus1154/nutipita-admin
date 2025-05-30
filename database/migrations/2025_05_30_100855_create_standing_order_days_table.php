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
        Schema::create('standing_order_days', function (Blueprint $table) {
            $table->increments('standing_order_day_id')->primary();
            $table->unsignedInteger('standing_order_id');
            $table->foreign('standing_order_id')->references('standing_order_id')->on('standing_orders');
            $table->unsignedTinyInteger('day');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('standing_order_days');
    }
};

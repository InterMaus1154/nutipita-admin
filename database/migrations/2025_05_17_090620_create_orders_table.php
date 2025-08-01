<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Enums\OrderStatus;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('order_id')->primary();
            $table->unsignedInteger('customer_id')->nullable();
            $table->foreign('customer_id')->references('customer_id')->on('customers')->onDelete('set null');
            $table->enum('order_status',
                array_map(fn($value) => $value->name, OrderStatus::cases()));
            // to determine if order is standing
            $table->boolean('is_standing')->default(false);
            $table->date('order_placed_at');
            $table->date('order_due_at');
            $table->timestamps();
        });

        DB::update('ALTER TABLE orders AUTO_INCREMENT = 200');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

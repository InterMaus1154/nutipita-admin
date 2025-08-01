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
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('customer_id')->primary();
            $table->string('customer_name', 250)->unique()->index();
            $table->string('customer_email', 250)->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->string('customer_address_1', 300);
            $table->string('customer_address_2', 300)->nullable();
            $table->string('customer_postcode', 10);
            $table->string('customer_city', 100);
            $table->string('customer_country', 200);
            $table->timestamps();
        });

        DB::update('ALTER TABLE customers AUTO_INCREMENT = 100');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

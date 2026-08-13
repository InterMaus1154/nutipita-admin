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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_address_1', 300)->nullable(true)->change();
            $table->string('customer_postcode', 10)->nullable(true)->change();
            $table->string('customer_city', 100)->nullable(true)->change();
            $table->string('customer_country', 200)->nullable(true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('customer_address_1', 300)->nullable(false)->change();
            $table->string('customer_postcode', 10)->nullable(false)->change();
            $table->string('customer_city', 100)->nullable(false)->change();
            $table->string('customer_country', 200)->nullable(false)->change();
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER TABLE invoice_products DROP PRIMARY KEY, ADD PRIMARY KEY (invoice_id, product_id, product_unit_price)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('ALTER TABLE invoice_products DROP PRIMARY KEY, ADD PRIMARY KEY(invoice_id, product_id)');
    }
};

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
        Schema::table('invoices', function (Blueprint $table) {
            $enumNames = implode("','", array_map(fn($case) => $case->name, \App\Enums\InvoiceStatus::cases()));

            DB::statement("ALTER TABLE invoices MODIFY COLUMN invoice_status ENUM('$enumNames')");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumn('invoice_status');
        });
    }
};

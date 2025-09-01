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
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->increments('credit_note_id')->primary();
            $table->unsignedInteger('invoice_id');
            $table->foreign('invoice_id')->references('invoice_id')->on('invoices');
            $table->date('credit_note_issued_at');
            $table->string('credit_note_number', 20);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};

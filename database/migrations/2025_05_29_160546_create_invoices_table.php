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
        Schema::create('invoices', function (Blueprint $table) {
            $table->increments('invoice_id')->primary();
            $table->string('invoice_number')->index()->unique();
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('customer_id')->on('customers');
            $table->date('invoice_issue_date');
            $table->date('invoice_due_date');
            $table->date('invoice_from');
            $table->date('invoice_to');
            $table->string('invoice_path', 300);
            $table->string('invoice_name', 150);
            $table->enum('invoice_status', ['paid', 'due'])->default('due');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

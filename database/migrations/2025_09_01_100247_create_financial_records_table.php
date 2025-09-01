<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\FinancialRecordType;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_records', function (Blueprint $table) {
            $table->increments('fin_record_id')->primary();
            $table->unsignedInteger('fin_cat_id')->nullable();
            $table->foreign('fin_cat_id')->references('fin_cat_id')->on('financial_categories')->onDelete('SET NULL');
            $table->string('fin_record_name', 500);
            $table->decimal('fin_record_amount', 10, 2);
            $table->date('fin_record_date');
            $table->enum('fin_record_type', array_map(fn(FinancialRecordType $type) => $type->name, FinancialRecordType::cases()));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_records');
    }
};

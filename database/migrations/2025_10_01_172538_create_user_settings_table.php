<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\settings\UserColorMode;
use App\Enums\settings\UserThemeMode;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->unsignedInteger('user_id')->primary();
            $table->foreign('user_id')->references('user_id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->unsignedSmallInteger('user_color_mode')->default(UserColorMode::ORANGE->value);
            $table->unsignedSmallInteger('user_theme_mode')->default(UserThemeMode::DARK->value);
            $table->unsignedSmallInteger('user_font_size')->default(16);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_settings');
    }
};

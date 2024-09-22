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
        Schema::create('font_group_fonts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('font_group_id');
            $table->unsignedBigInteger('font_id');
            $table->foreign('font_group_id')->references('id')->on('font_groups')->onDelete('cascade');
            $table->foreign('font_id')->references('id')->on('fonts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('font_group_fonts');
    }
};

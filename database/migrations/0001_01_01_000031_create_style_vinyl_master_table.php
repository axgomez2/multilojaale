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
        Schema::create('style_vinyl_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('style_id');
            $table->unsignedBigInteger('vinyl_master_id');
            $table->timestamps();

            $table->foreign('style_id')->references('id')->on('styles')->onDelete('cascade');
            $table->foreign('vinyl_master_id')->references('id')->on('vinyl_masters')->onDelete('cascade');

            $table->unique(['style_id', 'vinyl_master_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('style_vinyl_master');
    }
};

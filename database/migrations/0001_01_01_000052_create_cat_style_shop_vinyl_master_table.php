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
        Schema::create('cat_style_shop_vinyl_master', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vinyl_master_id');
            $table->unsignedBigInteger('cat_style_shop_id');
            $table->timestamps();

            $table->foreign('vinyl_master_id')->references('id')->on('vinyl_masters')->onDelete('cascade');
            $table->foreign('cat_style_shop_id')->references('id')->on('cat_style_shop')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cat_style_shop_vinyl_master');
    }
};

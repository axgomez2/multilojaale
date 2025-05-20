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
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedBigInteger('brand_id');
            $table->unsignedBigInteger('equipment_category_id');
            $table->text('description')->nullable();
            $table->json('specifications')->nullable();
            $table->unsignedBigInteger('weight_id')->nullable();
            $table->unsignedBigInteger('dimension_id')->nullable();
            $table->integer('quantity')->default(0);
            $table->decimal('price', 10, 2);
            $table->string('sku')->unique();
            $table->boolean('is_new')->default(true);
            $table->decimal('buy_price', 10, 2)->nullable();
            $table->decimal('promotional_price', 10, 2)->nullable();
            $table->boolean('is_promotional')->default(false);
            $table->boolean('in_stock')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
            $table->foreign('equipment_category_id')->references('id')->on('equipment_categories')->onDelete('cascade');
            $table->foreign('weight_id')->references('id')->on('weights')->onDelete('set null');
            $table->foreign('dimension_id')->references('id')->on('dimensions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};

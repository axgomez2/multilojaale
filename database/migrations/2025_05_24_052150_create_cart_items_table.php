<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('cart_id');
            $table->unsignedBigInteger('vinyl_master_id');
            $table->unsignedInteger('quantity')->default(1);
            $table->boolean('saved_for_later')->default(false);
            $table->timestamps();
            
            // Índices e chaves estrangeiras
            $table->foreign('cart_id')
                  ->references('id')
                  ->on('carts')
                  ->onDelete('cascade');
                  
            $table->foreign('vinyl_master_id')
                  ->references('id')
                  ->on('vinyl_masters')
                  ->onDelete('cascade');
            
            // Garantir que um produto não seja adicionado duas vezes ao mesmo carrinho
            $table->unique(['cart_id', 'vinyl_master_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};

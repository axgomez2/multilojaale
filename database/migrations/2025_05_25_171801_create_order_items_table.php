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
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->unsignedBigInteger('vinyl_master_id');
            $table->string('name'); // Nome do produto no momento da compra
            $table->text('description')->nullable(); // Descrição do produto
            $table->string('sku')->nullable(); // SKU do produto
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // Preço unitário no momento da compra
            $table->decimal('original_price', 10, 2)->nullable(); // Preço original (se houver desconto)
            $table->decimal('discount', 10, 2)->default(0); // Valor do desconto aplicado
            $table->decimal('tax', 10, 2)->default(0); // Valor dos impostos
            $table->decimal('total_price', 10, 2); // Preço total (unitário * quantidade - desconto + impostos)
            $table->json('metadata')->nullable(); // Metadados adicionais (tamanho, cor, etc.)
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
                  
            $table->foreign('vinyl_master_id')
                  ->references('id')
                  ->on('vinyl_masters')
                  ->onDelete('restrict');
            
            // Índices para melhorar consultas
            $table->index('vinyl_master_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

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
        // Verificar se a coluna product_id já existe
        if (!Schema::hasColumn('cart_items', 'product_id')) {
            // Adicionar nova coluna product_id
            Schema::table('cart_items', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable();
            });
            
            // Adicionar chave estrangeira para a tabela products
            Schema::table('cart_items', function (Blueprint $table) {
                $table->foreign('product_id')
                    ->references('id')
                    ->on('products')
                    ->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Verificar se a coluna product_id existe
        if (Schema::hasColumn('cart_items', 'product_id')) {
            // Remover a chave estrangeira
            Schema::table('cart_items', function (Blueprint $table) {
                try {
                    $table->dropForeign(['product_id']);
                } catch (\Exception $e) {
                    // Ignorar erro se a chave não existir
                }
                
                // Remover a coluna
                $table->dropColumn('product_id');
            });
        }
    }
};
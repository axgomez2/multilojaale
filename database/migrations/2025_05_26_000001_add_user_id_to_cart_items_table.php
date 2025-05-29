<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\CartItem;
use App\Models\Cart;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Adicionar a coluna user_id como nullable inicialmente
        Schema::table('cart_items', function (Blueprint $table) {
            $table->uuid('user_id')->nullable()->after('id');
            
            // Adicionar índice para melhorar performance
            $table->index('user_id');
        });
        
        // Atualizar os registros existentes para preencher o user_id com base no carrinho
        CartItem::with('cart')->chunk(100, function ($items) {
            foreach ($items as $item) {
                if ($item->cart && $item->cart->user_id) {
                    $item->user_id = $item->cart->user_id;
                    $item->save();
                }
            }
        });
        
        // Tornar a coluna obrigatória após preencher todos os registros
        Schema::table('cart_items', function (Blueprint $table) {
            $table->uuid('user_id')->nullable(false)->change();
            
            // Adicionar chave estrangeira
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Remover a chave estrangeira primeiro
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};

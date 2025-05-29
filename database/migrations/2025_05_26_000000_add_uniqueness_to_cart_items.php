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
        // Primeiro, remover itens duplicados mantendo apenas um por carrinho+produto
        // Isso é feito via código SQL puro já que o Eloquent não é adequado para operações em lote complexas
        \DB::statement("DELETE t1 FROM cart_items t1 INNER JOIN cart_items t2 
            WHERE t1.id < t2.id 
            AND t1.cart_id = t2.cart_id 
            AND t1.vinyl_master_id = t2.vinyl_master_id");

        // Remover a coluna user_id se existir
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'user_id')) {
                // Verificar se existe uma chave estrangeira para user_id
                $sm = Schema::getConnection()->getDoctrineSchemaManager();
                $foreignKeys = $sm->listTableForeignKeys('cart_items');
                
                foreach ($foreignKeys as $foreignKey) {
                    if (in_array('user_id', $foreignKey->getColumns())) {
                        $table->dropForeign(['user_id']);
                        break;
                    }
                }
                
                // Remover a coluna
                $table->dropColumn('user_id');
            }
            
            // Garantir que cart_id não seja nulo
            $table->uuid('cart_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            // Adicionar a coluna user_id novamente (se precisar reverter)
            if (!Schema::hasColumn('cart_items', 'user_id')) {
                $table->uuid('user_id')->nullable()->after('id');
                
                // Adicionar a chave estrangeira para user_id se a tabela users existir
                if (Schema::hasTable('users')) {
                    $table->foreign('user_id')
                          ->references('id')
                          ->on('users')
                          ->onDelete('cascade');
                }
            }
            
            // Tornar cart_id opcional novamente
            $table->uuid('cart_id')->nullable()->change();
        });
    }
};

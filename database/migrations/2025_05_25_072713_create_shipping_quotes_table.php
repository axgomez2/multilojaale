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
        Schema::create('shipping_quotes', function (Blueprint $table) {
            $table->id();
            $table->uuid('quote_token')->unique(); // Token que o frontend pode guardar para resgatar depois
            $table->uuid('user_id')->nullable();
            // Criando a foreign key manualmente em vez de usar constrained()
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
            $table->string('session_id')->nullable(); // Para usuários não autenticados
            $table->string('cart_items_hash')->index(); // Hash para identificar rapidamente itens do carrinho
            $table->json('cart_items'); // Itens do carrinho (id, qty)
            $table->string('zip_from');
            $table->string('zip_to');
            $table->json('products'); // Produtos preparados para envio
            $table->json('api_response')->nullable(); // Resposta bruta da API (opcional)
            $table->json('options')->nullable(); // Opções formatadas (ex: PAC, SEDEX)
            $table->string('selected_service_id')->nullable(); // Serviço escolhido no frontend
            $table->decimal('selected_price', 10, 2)->nullable();
            $table->integer('selected_delivery_time')->nullable();
            $table->timestamp('expires_at')->nullable(); // Data de expiração da cotação
            $table->timestamps();
            
            // Índices para melhorar performance
            $table->index(['user_id']);
            $table->index(['session_id']);
            $table->index(['zip_from', 'zip_to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipping_quotes');
    }
};

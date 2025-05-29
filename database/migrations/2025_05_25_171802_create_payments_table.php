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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('gateway_id')->nullable();
            $table->string('gateway_code'); // mercadopago, pagseguro, etc
            $table->string('method');  // credit_card, pix, boleto
            $table->string('status');
            $table->string('transaction_id')->nullable();
            $table->string('payment_method_id')->nullable(); // ID do método de pagamento no gateway
            $table->decimal('amount', 10, 2);
            $table->decimal('net_amount', 10, 2)->nullable(); // Valor líquido após taxas
            $table->decimal('fee', 10, 2)->nullable(); // Taxa cobrada pelo gateway
            $table->string('installments')->default(1); // Número de parcelas
            $table->json('gateway_data')->nullable(); // Resposta bruta do gateway
            $table->json('metadata')->nullable(); // Metadados adicionais
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->timestamps();
            
            // Chaves estrangeiras
            $table->foreign('order_id')
                  ->references('id')
                  ->on('orders')
                  ->onDelete('cascade');
                  
            $table->foreign('gateway_id')
                  ->references('id')
                  ->on('payment_gateways')
                  ->onDelete('set null');
            
            // Índices para melhorar consultas
            $table->index('status');
            $table->index('transaction_id');
            $table->index('gateway_code');
            $table->index('payment_method_id');
            $table->index('paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

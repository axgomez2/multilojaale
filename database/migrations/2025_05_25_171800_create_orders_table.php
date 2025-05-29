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
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('order_number')->unique(); // Número do pedido formatado (ex: PED-20230526-0001)
            $table->uuid('user_id')->nullable();
            $table->string('session_id')->nullable();
            $table->string('status'); // pending, processing, on_hold, completed, cancelled, refunded, failed
            $table->string('payment_status')->default('pending'); // pending, paid, partially_paid, refunded, partially_refunded, voided
            $table->string('shipping_status')->default('pending'); // pending, processing, shipped, delivered, cancelled
            
            // Valores monetários
            $table->decimal('subtotal', 10, 2); // Soma dos itens
            $table->decimal('shipping', 10, 2); // Frete
            $table->decimal('discount', 10, 2)->default(0); // Descontos
            $table->decimal('tax', 10, 2)->default(0); // Impostos
            $table->decimal('total', 10, 2); // Total final
            
            // Informações de envio
            $table->uuid('shipping_quote_id')->nullable();
            $table->uuid('shipping_address_id')->nullable();
            $table->uuid('billing_address_id')->nullable();
            $table->string('shipping_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('tracking_url')->nullable();
            
            // Informações do cliente
            $table->string('customer_note')->nullable(); // Nota do cliente
            $table->string('customer_ip_address', 45)->nullable();
            $table->string('customer_user_agent')->nullable();
            
            // Cupom de desconto
            $table->string('coupon_code')->nullable();
            $table->decimal('coupon_discount', 10, 2)->default(0);
            
            // Datas importantes
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            
            // Índices para melhorar consultas
            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('payment_status');
            $table->index('shipping_status');
            $table->index('order_number');
            $table->index('created_at');
            
            // Chaves estrangeiras
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('shipping_address_id')
                  ->references('id')
                  ->on('addresses')
                  ->onDelete('set null');
                  
            $table->foreign('billing_address_id')
                  ->references('id')
                  ->on('addresses')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};

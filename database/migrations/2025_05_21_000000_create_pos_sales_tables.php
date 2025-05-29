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
        // Tabela principal de vendas no PDV
        if (!Schema::hasTable('pos_sales')) {
            Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->nullable(); // Usuário cadastrado (opcional)
            $table->string('customer_name')->nullable(); // Nome do cliente não cadastrado
            $table->decimal('subtotal', 10, 2);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('shipping', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->string('payment_method')->default('money'); // método de pagamento (dinheiro, cartão, etc)
            $table->text('notes')->nullable(); // Observações da venda
            $table->string('invoice_number')->unique(); // Número único para a nota
            $table->timestamps();
            
            // Adicionando a chave estrangeira separadamente
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Tabela de itens da venda
        if (!Schema::hasTable('pos_sale_items')) {
            Schema::create('pos_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_sale_id');
            $table->unsignedBigInteger('vinyl_sec_id');
            $table->decimal('price', 10, 2); // Preço no momento da venda
            $table->integer('quantity')->default(1);
            $table->decimal('item_discount', 10, 2)->default(0); // Desconto por item
            $table->decimal('item_total', 10, 2); // Total do item
            $table->timestamps();
            
            // Adicionando as chaves estrangeiras separadamente
            $table->foreign('pos_sale_id')->references('id')->on('pos_sales')->onDelete('cascade');
            $table->foreign('vinyl_sec_id')->references('id')->on('vinyl_secs')->onDelete('restrict');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sale_items');
        Schema::dropIfExists('pos_sales');
    }
};

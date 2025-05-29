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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            
            // Identificação
            $table->string('name', 50)->comment('Apelido do endereço (ex: Casa, Trabalho)');
            $table->enum('type', ['shipping', 'billing', 'both'])->default('both')->comment('Tipo: shipping (entrega), billing (cobrança), both (ambos)');
            
            // Dados do destinatário
            $table->string('recipient_name', 100)->comment('Nome do destinatário');
            $table->string('recipient_document', 20)->nullable()->comment('CPF/CNPJ do destinatário');
            $table->string('recipient_phone', 20)->comment('Telefone de contato');
            $table->string('recipient_email', 100)->nullable()->comment('E-mail para notificações');
            
            // Endereço
            $table->string('zipcode', 9)->comment('CEP (formato: 00000-000)');
            $table->string('street', 200)->comment('Logradouro');
            $table->string('number', 20)->comment('Número');
            $table->string('complement', 100)->nullable()->comment('Complemento');
            $table->string('district', 100)->comment('Bairro');
            $table->string('city', 100)->comment('Cidade');
            $table->string('state', 2)->comment('Estado (UF)');
            $table->string('country', 2)->default('BR')->comment('País (código ISO 3166-1 alpha-2)');
            
            // Informações adicionais
            $table->text('reference')->nullable()->comment('Ponto de referência');
            $table->json('additional_data')->nullable()->comment('Dados adicionais em formato JSON');
            
            // Status
            $table->boolean('is_default_shipping')->default(false)->comment('Endereço de entrega padrão');
            $table->boolean('is_default_billing')->default(false)->comment('Endereço de cobrança padrão');
            $table->boolean('is_active')->default(true)->comment('Endereço ativo');
            
            // Auditoria
            $table->string('created_by')->nullable()->comment('Quem criou o registro');
            $table->string('updated_by')->nullable()->comment('Quem atualizou por último');
            $table->timestamps();
            $table->softDeletes();
            
            // Chaves estrangeiras
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
            
            // Índices
            $table->index(['user_id', 'type']);
            $table->index(['user_id', 'is_default_shipping']);
            $table->index(['user_id', 'is_default_billing']);
            $table->index('zipcode');
            $table->index('city');
            $table->index('state');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};

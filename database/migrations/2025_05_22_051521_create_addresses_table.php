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
            $table->id();
            $table->uuid('user_id');
            $table->string('name', 50)->comment('Apelido do endereço (ex: Casa, Trabalho)');
            $table->string('recipient', 100)->comment('Nome do destinatário para entrega');
            $table->string('type', 20)->default('residential')->comment('Tipo: residential, commercial, other');
            $table->string('zipcode', 9)->comment('CEP');
            $table->string('state', 2)->comment('Estado (UF)');
            $table->string('city', 100)->comment('Cidade');
            $table->string('district', 100)->comment('Bairro');
            $table->string('street', 200)->comment('Logradouro');
            $table->string('number', 20)->comment('Número');
            $table->string('complement', 100)->nullable()->comment('Complemento');
            $table->text('reference')->nullable()->comment('Ponto de referência');
            $table->string('phone', 20)->nullable()->comment('Telefone de contato para entrega');
            $table->boolean('is_default')->default(false)->comment('Endereço padrão para entrega');
            $table->boolean('is_active')->default(true)->comment('Endereço ativo');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['user_id', 'is_default', 'is_active']);
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

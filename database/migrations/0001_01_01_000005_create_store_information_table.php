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
        Schema::create('store_information', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('document_type')->default('cnpj'); // 'cnpj' ou 'cpf'
            $table->string('document')->nullable();
            $table->string('address')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('state', 2)->nullable(); // UF com 2 caracteres
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_information');
    }
};

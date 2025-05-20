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
        Schema::create('vinyl_secs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vinyl_master_id');
            $table->string('catalog_number')->nullable();
            $table->string('barcode')->nullable()->comment('Código de barras oficial do produto');
            $table->string('internal_code')->nullable()->unique()->comment('Código interno da loja');
            $table->unsignedBigInteger('weight_id')->nullable();
            $table->unsignedBigInteger('dimension_id')->nullable();
            $table->unsignedBigInteger('midia_status_id')->nullable();
            $table->unsignedBigInteger('cover_status_id')->nullable();
            $table->unsignedBigInteger('supplier_id')->nullable();
            $table->integer('stock')->default(0);
            $table->decimal('price', 10, 2);
            $table->string('format')->nullable();
            $table->integer('num_discs')->default(1);
            $table->string('speed')->nullable();
            $table->string('edition')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_new')->default(true);
            $table->decimal('buy_price', 10, 2)->nullable();
            $table->decimal('promotional_price', 12, 2)->nullable()->comment('Preço promocional');
            $table->boolean('is_promotional')->default(false);
            $table->dateTime('promo_starts_at')->nullable()->comment('Data de início da promoção');
            $table->dateTime('promo_ends_at')->nullable()->comment('Data de término da promoção');
            $table->boolean('in_stock')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // Índices para melhorar a performance das consultas
            $table->index('supplier_id');
            $table->index(['price', 'is_promotional']);
            
            // Chaves estrangeiras
            $table->foreign('vinyl_master_id')->references('id')->on('vinyl_masters')->onDelete('cascade');
            $table->foreign('weight_id')->references('id')->on('weights')->onDelete('set null');
            $table->foreign('dimension_id')->references('id')->on('dimensions')->onDelete('set null');
            $table->foreign('midia_status_id')->references('id')->on('midia_status')->onDelete('set null');
            $table->foreign('cover_status_id')->references('id')->on('cover_status')->onDelete('set null');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vinyl_secs');
    }
};

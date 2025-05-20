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
        Schema::create('vinyl_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vinyl_master_id')->constrained('vinyl_masters')->onDelete('cascade');
            $table->uuid('user_uuid')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('country', 2)->nullable()->comment('Código do país ISO 3166-1 alpha-2');
            $table->string('region', 100)->nullable()->comment('Estado/Província');
            $table->string('city', 100)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('browser', 100)->nullable();
            $table->string('platform', 100)->nullable();
            $table->string('device_type', 20)->nullable()->comment('desktop, mobile, tablet');
            $table->timestamp('viewed_at');
            $table->timestamps();
            
            // Índices para melhorar a performance das consultas
            $table->index(['vinyl_master_id', 'viewed_at']);
            $table->index(['user_uuid']);
            $table->index(['country']);
            $table->index(['region']);
            $table->foreign('user_uuid')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vinyl_views');
    }
};

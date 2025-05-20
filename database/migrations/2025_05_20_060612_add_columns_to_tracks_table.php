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
        Schema::table('tracks', function (Blueprint $table) {
            // Adicionar posição da faixa se ainda não existir
            if (!Schema::hasColumn('tracks', 'position')) {
                $table->integer('position')->default(0)->after('name');
            }
            
            // Adicionar duração em segundos
            if (!Schema::hasColumn('tracks', 'duration_seconds')) {
                $table->integer('duration_seconds')->nullable()->after('duration');
            }
            
            // Adicionar informações extras como JSON
            if (!Schema::hasColumn('tracks', 'extra_info')) {
                $table->json('extra_info')->nullable()->after('youtube_url');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tracks', function (Blueprint $table) {
            // Remover colunas adicionadas
            $columns = ['position', 'duration_seconds', 'extra_info'];
            $existingColumns = [];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('tracks', $column)) {
                    $existingColumns[] = $column;
                }
            }
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};

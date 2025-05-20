<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Verificar se a coluna position existe
        if (Schema::hasColumn('tracks', 'position')) {
            // Deletar a coluna para voltar a criar corretamente
            Schema::table('tracks', function (Blueprint $table) {
                $table->dropColumn('position');
            });
        }
        
        // Adicionar novamente a coluna position com a definição correta
        Schema::table('tracks', function (Blueprint $table) {
            $table->integer('position')->default(0)->after('name');
        });
        
        // Garantir que a coluna duration_seconds existe
        if (!Schema::hasColumn('tracks', 'duration_seconds')) {
            Schema::table('tracks', function (Blueprint $table) {
                $table->integer('duration_seconds')->nullable()->after('duration');
            });
        }
        
        // Garantir que a coluna extra_info existe
        if (!Schema::hasColumn('tracks', 'extra_info')) {
            Schema::table('tracks', function (Blueprint $table) {
                $table->json('extra_info')->nullable()->after('youtube_url');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Não reverter a correção da coluna position, pois isso quebraria a estrutura
    }
};

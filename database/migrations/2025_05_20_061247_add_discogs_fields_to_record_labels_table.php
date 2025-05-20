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
        Schema::table('record_labels', function (Blueprint $table) {
            // Adicionar ID do Discogs
            if (!Schema::hasColumn('record_labels', 'discogs_id')) {
                $table->string('discogs_id')->nullable()->unique()->after('slug');
            }
            
            // Adicionar URL do perfil no Discogs
            if (!Schema::hasColumn('record_labels', 'discogs_url')) {
                $table->string('discogs_url')->nullable()->after('discogs_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('record_labels', function (Blueprint $table) {
            // Remover colunas adicionadas
            $columns = ['discogs_id', 'discogs_url'];
            $existingColumns = [];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('record_labels', $column)) {
                    $existingColumns[] = $column;
                }
            }
            
            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};

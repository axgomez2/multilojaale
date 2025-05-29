<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ResetDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Desativar verificação de chaves estrangeiras
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Lista de tabelas na ordem correta para evitar erros de chave estrangeira
        $tables = [
            'vinyl_views',
            'cart_items',
            'carts',
            'vinyl_masters',
            'payment_settings',
            'payment_gateways',
            'payments',
            'order_items',
            'orders',
            'shipping_quotes',
            'addresses',
            'wishlists',
            'wantlists',
            'style_vinyl_master',
            'artist_vinyl_master',
            'tracks',
            'vinyl_secs',
            'equipment',
            'media',
            'products',
            'cat_style_shop_vinyl_master',
            'cat_style_shop',
            'product_types',
            'record_labels',
            'styles',
            'artists',
            'weights',
            'dimensions',
            'brands',
            'equipment_categories',
            'suppliers',
            'cover_status',
            'midia_status',
            'user_permissions',
            'users',
            'jobs',
            'cache',
            'store_information',
            'migrations',
        ];

        // Limpar todas as tabelas
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->truncate();
            }
        }

        // Reativar verificação de chaves estrangeiras
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Todas as tabelas foram limpas com sucesso!');
    }
}

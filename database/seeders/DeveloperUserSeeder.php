<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserPermission;

class DeveloperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria o usuário desenvolvedor com role de admin (66)
        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => 'Alexandre Gomes',
            'email' => 'axgomezprogramador@gmail.com',
            'email_verified_at' => now(),
            'password' => Hash::make('Ale123!@'), // Senha padrão
            'role' => 66, // Role de administrador
            'remember_token' => Str::random(10),
        ]);
        
        // Adiciona a permissão de desenvolvedor
        UserPermission::create([
            'user_id' => $user->id,
            'is_developer' => true,
        ]);
        
        $this->command->info('Usuário desenvolvedor criado com sucesso!');
        $this->command->info('Email: axgomezprogramador@gmail.com');
        $this->command->info('Senha: Ale123!@');
        $this->command->info('Credenciais configuradas para o ambiente.');
    }
}

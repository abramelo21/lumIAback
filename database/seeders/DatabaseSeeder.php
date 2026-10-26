<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@lumia.local'],
            [
                'name' => 'Administrador LumiA',
                'password' => 'password',
                'role' => UserRole::Admin,
            ]
        );

        User::query()->updateOrCreate(
            ['email' => 'aluno@lumia.local'],
            [
                'name' => 'Participante LumiA',
                'password' => 'password',
                'role' => UserRole::Participant,
            ]
        );
    }
}

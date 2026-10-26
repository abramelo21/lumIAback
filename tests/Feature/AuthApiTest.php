<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_participant_can_register_with_consent(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Aluno FUMEC',
            'email' => 'aluno@fumec.br',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'consent' => true,
        ]);

        $response->assertCreated()->assertJsonPath('user.role', 'participant');
        $this->assertDatabaseHas('consents', ['purpose' => 'orientacao_curricular']);
    }

    public function test_user_can_login_and_access_profile(): void
    {
        $user = User::factory()->create([
            'email' => 'aluno@fumec.br',
            'password' => 'secret123',
        ]);

        $login = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'secret123',
        ])->assertOk();

        $token = $login->json('token');

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('user.email', 'aluno@fumec.br');
    }
}

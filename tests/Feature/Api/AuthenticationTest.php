<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_authenticate()
    {
        $this->json('POST', route('authenticate.login'), [
            'email' => 'test@email.com',
            'password' => 'password',
            'password_client' => 1
        ], ['Accept' => 'application/json'])
            ->assertOk()
            ->assertJsonStructure([
            'expires',
            'access_token'
        ]);
    }
}

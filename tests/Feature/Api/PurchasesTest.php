<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PurchasesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_cannot_purchase_while_unauthenticated()
    {
        $this->json('POST', 'http://0.0.0.0/api/products/1?purchase', [], ['Accept' => 'application/json'])
            ->assertStatus(401);
    }

    public function test_can_purchase_after_authentication()
    {
        $this->withoutExceptionHandling();

        $authRequest = $this->json('POST', route('authenticate.login'), [
            'email' => 'test@email.com',
            'password' => 'password',
            'password_client' => 1
        ], ['Accept' => 'application/json']);

        if ($authRequest->isOk()) {
            $response = json_decode($authRequest->getContent());

            $this->json('POST', 'http://0.0.0.0/api/products/1?purchase', [], [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $response->access_token
                ])->assertOk();
        } else {
            // test failed
            $this->assertTrue(false);
        }
    }
}

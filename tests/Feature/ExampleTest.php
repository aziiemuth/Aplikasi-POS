<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_redirects_to_login(): void
    {
        $response = $this->get('/');

        // Root URL will redirect to login page for unauthenticated users
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }
}

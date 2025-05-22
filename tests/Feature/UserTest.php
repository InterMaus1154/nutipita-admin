<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test unauthenticated user, should return 401
     */
    public function test_unauthenticated_user_on_dashboard_expect_401(): void
    {
        $response = $this->get('/');
        $response->assertStatus(401);
    }

    /**
     * Test authenticated user, should return 200
     */
    public function test_authenticated_user_on_dashboard_expect_200()
    {
        $user = User::first();
        $this->actingAs($user);
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}

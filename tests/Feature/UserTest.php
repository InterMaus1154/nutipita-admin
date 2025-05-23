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
    public function test_unauthenticated_user_on_dashboard_expect_redirect_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('auth.view.login'));
    }

    /**
     * Test authenticated user, should return 200
     */
    public function test_authenticated_user_on_dashboard_expect_200_admin_index()
    {
        $user = User::first();
        $this->actingAs($user);
        $response = $this->get('/');
        $response->assertStatus(200)->assertViewIs('admin.index');
    }
}

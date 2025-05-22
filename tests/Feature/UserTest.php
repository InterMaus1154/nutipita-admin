<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * Test unauthenticated user
     */
    public function test_unauthenticated_user_expect_401(): void
    {
        $response = $this->get('/');
        $response->assertStatus(401);

    }
}

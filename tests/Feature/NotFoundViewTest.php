<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotFoundViewTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_it_displays_custom_error_message_from_session(): void
    {
        $this->actingAs(User::first());

        $response = $this->withSession(['error_message' => 'Customer not found'])->get('error/404');

        $response->assertStatus(404);
    }
}

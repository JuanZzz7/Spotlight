<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_ana_can_login_and_access_dashboard(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::where('email', 'ana@spotlight.com')->first();
        $this->assertNotNull($user, 'User ana@spotlight.com does not exist');

        $response = $this->post('/login', [
            'email' => 'ana@spotlight.com',
            'password' => 'password',
            'role' => 'user',
        ]);

        $response->assertStatus(302);
        $redirectUrl = $response->headers->get('Location');
        echo "\nRedirect URL: " . $redirectUrl . "\n";

        $followResponse = $this->actingAs($user)->get($redirectUrl);
        echo "Follow Status: " . $followResponse->status() . "\n";
        $followResponse->assertStatus(200);
    }

    public function test_user_can_access_panel(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'ana@spotlight.com')->first();
        $response = $this->actingAs($user)->get('/user/panel');
        $response->assertStatus(200);
    }
}

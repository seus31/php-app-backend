<?php

namespace Feature\Http\Controllers\Api\V1;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_returns_422_when_name_is_missing()
    {
        $response = $this->postJson('/api/v1/register', [
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422);
    }

    public function test_register_returns_422_when_email_is_invalid()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422);
    }

    public function test_register_returns_422_when_password_is_less_than_8_characters()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short',
            'password_confirmation' => 'short'
        ]);

        $response->assertStatus(422);
    }

    public function test_register_returns_422_when_password_confirmation_doesnt_match()
    {
        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'differentpassword'
        ]);

        $response->assertStatus(422);
    }

    public function test_register_returns_422_when_email_is_already_taken()
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]);

        $response->assertStatus(422);
    }

    public function test_register_creates_new_user_with_correct_details()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token', 'token_type']);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);

        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    public function test_register_hashes_password_before_storing()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201);

        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user);
        $this->assertNotEquals($userData['password'], $user->password);
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    public function test_register_returns_201_on_successful_registration()
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token', 'token_type']);

        $this->assertDatabaseHas('users', [
            'name' => $userData['name'],
            'email' => $userData['email']
        ]);

        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    public function test_register_returns_json_response_with_access_token_and_token_type()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ])
            ->assertJson([
                'token_type' => 'Bearer'
            ]);

        $this->assertNotEmpty($response->json('access_token'));
    }

    public function test_register_creates_personal_access_token_for_user()
    {
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ];

        $response = $this->postJson('/api/v1/register', $userData);

        $response->assertStatus(201)
            ->assertJsonStructure(['access_token', 'token_type']);

        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'auth_token'
        ]);

        $this->assertEquals(1, $user->tokens()->count());
    }
}

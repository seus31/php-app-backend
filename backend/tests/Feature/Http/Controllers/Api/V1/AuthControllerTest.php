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

    public function test_login_validates_email_format()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'invalid-email',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
    }

    public function test_login_returns_422_when_email_is_missing()
    {
        $response = $this->postJson('/api/v1/login', [
            'password' => 'password123'
        ]);

        $response->assertStatus(422);
    }

    public function test_login_returns_422_when_password_is_missing()
    {
        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com'
        ]);

        $response->assertStatus(422);
    }

    public function test_login_returns_422_with_error_message_for_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'incorrectpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The provided credentials are incorrect.'
            ]);
    }


    public function test_login_authenticates_user_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'correctpassword',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ])
            ->assertJson([
                'token_type' => 'Bearer'
            ]);

        $this->assertNotEmpty($response->json('access_token'));
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'auth_token'
        ]);
    }

    public function test_login_returns_json_response_with_access_token_and_token_type_for_successful_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ])
            ->assertJson([
                'token_type' => 'Bearer'
            ]);

        $this->assertNotEmpty($response->json('access_token'));
    }

    public function test_login_creates_new_personal_access_token_for_user()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);

        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'auth_token'
        ]);

        $this->assertEquals(1, $user->tokens()->count());
    }

    public function test_login_returns_bearer_as_token_type()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ])
            ->assertJson([
                'token_type' => 'Bearer'
            ]);
    }

    public function test_login_does_not_create_token_for_failed_authentication()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('correctpassword'),
        ]);

        $response = $this->postJson('/api/v1/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'message' => 'The provided credentials are incorrect.'
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_type' => User::class,
            'tokenable_id' => $user->id,
            'name' => 'auth_token'
        ]);

        $this->assertEquals(0, $user->tokens()->count());
    }

    public function test_login_handles_case_sensitive_email_addresses_correctly()
    {
        $email = 'Test@Example.com';
        $password = 'password123';

        User::factory()->create([
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // Attempt login with lowercase email
        $response = $this->postJson('/api/v1/login', [
            'email' => strtolower($email),
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);

        // Attempt login with uppercase email
        $response = $this->postJson('/api/v1/login', [
            'email' => strtoupper($email),
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);

        // Attempt login with mixed case email
        $response = $this->postJson('/api/v1/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'token_type'
            ]);
    }

    public function test_logout_returns_200_and_success_message_for_valid_user()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);
    }

    public function test_logout_deletes_current_access_token()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        $this->actingAs($user);
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
        ]);

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);
    }

    public function test_logout_returns_401_for_invalid_token()
    {
        $invalidToken = 'invalid_token_string';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $invalidToken,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_logout_handles_request_when_user_has_no_active_tokens()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $user->tokens()->delete();

        $response = $this->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);

        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_logout_returns_correct_response_structure()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(200)
            ->assertJsonStructure(['message'])
            ->assertJson([
                'message' => 'Logged out successfully'
            ]);
    }

    public function test_logout_handles_request_for_nonexistent_user()
    {
        $nonExistentUserId = 9999;
        $fakeToken = 'fake_token_123';

        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $fakeToken,
        ])->postJson('/api/v1/logout');

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);

        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $nonExistentUserId,
            'tokenable_type' => User::class,
        ]);
    }
}

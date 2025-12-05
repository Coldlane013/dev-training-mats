<?php

use App\Models\User;
use App\Service\UserService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can create a user', function () {
    // Create and authenticate a user
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'first_name' => 'John',
        'last_name' => 'Doe',
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->postJson('/api/v1/users', $userData);

    $response->assertStatus(200)
            ->assertJson([
                'message' => 'User Created Successful'
            ]);
});

it('can list users with pagination', function () {
    // Create and authenticate a user
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    // Create additional users
    User::factory()->count(5)->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->getJson('/api/v1/users');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'user' => [
                    'data',
                    'current_page',
                    'per_page',
                    'total'
                ],
                'data',
                'logged_in_stats'
            ])
            ->assertJson([
                'message' => 'User lists successfully fetched.',
            ]);
});

it('can show a specific user', function () {
    // Create and authenticate a user
    $authenticatedUser = User::factory()->create();
    $token = $authenticatedUser->createToken('test-token')->plainTextToken;

    // Create another user to fetch
    $user = User::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->getJson("/api/v1/users/{$user->id}");

    $response->assertStatus(200)
            ->assertJson([
                'message' => 'User Fetched Successful',
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ]);
});

it('can update a user', function () {
    // Create and authenticate a user
    $authenticatedUser = User::factory()->create();
    $token = $authenticatedUser->createToken('test-token')->plainTextToken;

    // Create another user to update
    $user = User::factory()->create();

    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => 'newpassword123',
        'first_name' => 'Updated',
        'last_name' => 'Name',
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->putJson("/api/v1/users/{$user->id}", $updateData);

    $response->assertStatus(200)
            ->assertJson([
                'message' => 'User Successful Updated'
            ]);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'first_name' => 'Updated',
        'last_name' => 'Name',
    ]);
});

it('can delete a user', function () {
    // Create and authenticate a user
    $authenticatedUser = User::factory()->create();
    $token = $authenticatedUser->createToken('test-token')->plainTextToken;

    // Create another user to delete
    $user = User::factory()->create();

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->deleteJson("/api/v1/users/{$user->id}");

    $response->assertStatus(200)
            ->assertJson([
                'message' => 'User Successful Deleted'
            ]);

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});

it('validates required fields when creating user', function () {
    // Create and authenticate a user
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->postJson('/api/v1/users', []);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'password', 'first_name', 'last_name']);
});

it('validates email format when creating user', function () {
    // Create and authenticate a user
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $userData = [
        'name' => 'Invalid Email User',
        'email' => 'invalid-email',
        'password' => 'password123',
        'first_name' => 'Invalid',
        'last_name' => 'Email'
    ];

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->postJson('/api/v1/users', $userData);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
});

it('requires authentication for all endpoints', function () {
    // Test index without auth
    $this->getJson('/api/v1/users')->assertStatus(401);

    // Test store without auth
    $this->postJson('/api/v1/users', [])->assertStatus(401);

    // Test show without auth
    $this->getJson('/api/v1/users/1')->assertStatus(401);

    // Test update without auth
    $this->putJson('/api/v1/users/1', [])->assertStatus(401);

    // Test delete without auth
    $this->deleteJson('/api/v1/users/1')->assertStatus(401);
});

it('returns 403 when user not found', function () {
    // Create and authenticate a user
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->getJson('/api/v1/users/999');

    $response->assertStatus(403)
            ->assertJson([
                'message' => 'User not found',
            ]);
});

it('returns 403 when no users exist for index', function () {
    // Create and authenticate a user
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ])->getJson('/api/v1/users');

    $response->assertStatus(403)
            ->assertJson([
                'message' => 'No users found.',
            ]);
});

<?php

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;

test('redirect endpoint returns a json url pointing to google', function (): void {
    $response = $this->getJson('/api/v1/auth/google/redirect')
        ->assertSuccessful()
        ->assertJsonStructure(['url']);

    expect($response->json('url'))->toContain('accounts.google.com');
});

test('callback creates a new user and returns a token', function (): void {
    $socialiteUser = (new SocialiteUser)->map([
        'id' => 'google-abc123',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'avatar' => 'https://example.com/avatar.jpg',
    ]);

    Socialite::shouldReceive('driver->stateless->user')->andReturn($socialiteUser);

    $this->getJson('/api/v1/auth/google/callback')
        ->assertSuccessful()
        ->assertJsonStructure(['token']);

    $this->assertDatabaseHas('users', [
        'google_id' => 'google-abc123',
        'email' => 'jane@example.com',
    ]);
});

test('callback finds existing user by google_id and returns token without creating a duplicate', function (): void {
    User::factory()->create([
        'google_id' => 'google-abc123',
        'email' => 'jane@example.com',
    ]);

    $socialiteUser = (new SocialiteUser)->map([
        'id' => 'google-abc123',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'avatar' => 'https://example.com/avatar.jpg',
    ]);

    Socialite::shouldReceive('driver->stateless->user')->andReturn($socialiteUser);

    $this->getJson('/api/v1/auth/google/callback')
        ->assertSuccessful()
        ->assertJsonStructure(['token']);

    expect(User::count())->toBe(1);
});

test('callback returns 422 when google throws an exception', function (): void {
    Socialite::shouldReceive('driver->stateless->user')->andThrow(new Exception('Invalid state'));

    $this->getJson('/api/v1/auth/google/callback')
        ->assertUnprocessable()
        ->assertJson(['message' => 'Invalid Google credentials']);
});

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

test('callback creates a new user and redirects to nativephp scheme with token', function (): void {
    $socialiteUser = (new SocialiteUser)->map([
        'id' => 'google-abc123',
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'avatar' => 'https://example.com/avatar.jpg',
    ]);

    Socialite::shouldReceive('driver->stateless->user')->andReturn($socialiteUser);

    $response = $this->get('/api/v1/auth/google/callback')
        ->assertRedirect();

    expect($response->headers->get('Location'))->toStartWith('nativephp://127.0.0.1/auth/callback?token=');

    $this->assertDatabaseHas('users', [
        'google_id' => 'google-abc123',
        'email' => 'jane@example.com',
    ]);
});

test('callback finds existing user by google_id and redirects with token without creating a duplicate', function (): void {
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

    $response = $this->get('/api/v1/auth/google/callback')
        ->assertRedirect();

    expect($response->headers->get('Location'))->toStartWith('nativephp://127.0.0.1/auth/callback?token=');

    expect(User::count())->toBe(1);
});

test('callback redirects to nativephp scheme with error when google throws an exception', function (): void {
    Socialite::shouldReceive('driver->stateless->user')->andThrow(new Exception('Invalid state'));

    $response = $this->get('/api/v1/auth/google/callback')
        ->assertRedirect();

    expect($response->headers->get('Location'))
        ->toBe('nativephp://127.0.0.1/auth/callback?error=1&message='.urlencode('Google login failed'));
});

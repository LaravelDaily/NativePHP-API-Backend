<?php

use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

test('authenticated user can retrieve their info', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertExactJson([
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => null,
        ]);
});

test('me response includes local avatar url when avatar_path is set', function (): void {
    Storage::fake('public');

    $user = User::factory()->create(['avatar_path' => 'avatars/1.jpg']);
    Sanctum::actingAs($user, ['*']);

    $response = $this->getJson('/api/v1/auth/me')->assertOk();

    expect($response->json('avatar_url'))->toContain('avatars/1.jpg');
});

test('me response falls back to google avatar when only avatar is set', function (): void {
    $user = User::factory()->create(['avatar' => 'https://example.com/google-avatar.jpg']);
    Sanctum::actingAs($user, ['*']);

    $this->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonFragment(['avatar_url' => 'https://example.com/google-avatar.jpg']);
});

test('unauthenticated request returns 401', function (): void {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

test('unauthenticated request returns 401', function (): void {
    $this->postJson('/api/v1/profile/avatar')->assertUnauthorized();
});

test('valid avatar upload stores file and returns 204', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $file = UploadedFile::fake()->image('avatar.jpg');

    $this->postJson('/api/v1/profile/avatar', ['avatar' => $file])
        ->assertNoContent();

    Storage::disk('public')->assertExists("avatars/{$user->id}.jpg");

    expect($user->fresh()->avatar_path)->toBe("avatars/{$user->id}.jpg");
});

test('second upload overwrites the previous avatar', function (): void {
    Storage::fake('public');

    $user = User::factory()->create(['avatar_path' => 'avatars/old.jpg']);
    Sanctum::actingAs($user, ['*']);

    $file = UploadedFile::fake()->image('new-avatar.jpg');

    $this->postJson('/api/v1/profile/avatar', ['avatar' => $file])
        ->assertNoContent();

    Storage::disk('public')->assertExists("avatars/{$user->id}.jpg");
    expect($user->fresh()->avatar_path)->toBe("avatars/{$user->id}.jpg");
});

test('missing avatar field returns 422', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $this->postJson('/api/v1/profile/avatar', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['avatar']);
});

test('non-image file returns 422', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

    $this->postJson('/api/v1/profile/avatar', ['avatar' => $file])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['avatar']);
});

test('file exceeding 5mb returns 422', function (): void {
    Storage::fake('public');

    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $file = UploadedFile::fake()->image('large.jpg')->size(5121);

    $this->postJson('/api/v1/profile/avatar', ['avatar' => $file])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['avatar']);
});

<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('authenticated user can logout and token is deleted', function (): void {
    $user = User::factory()->create();
    Sanctum::actingAs($user, ['*']);

    $this->postJson('/api/v1/auth/logout')
        ->assertSuccessful()
        ->assertJson(['message' => 'Logged out successfully']);

    expect($user->tokens()->count())->toBe(0);
});

test('unauthenticated request to logout returns 401', function (): void {
    $this->postJson('/api/v1/auth/logout')->assertUnauthorized();
});

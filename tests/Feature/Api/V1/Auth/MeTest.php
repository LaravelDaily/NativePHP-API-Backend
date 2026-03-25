<?php

use App\Models\User;
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
        ]);
});

test('unauthenticated request returns 401', function (): void {
    $this->getJson('/api/v1/auth/me')->assertUnauthorized();
});

<?php

use App\Models\Registration;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('unauthenticated request returns 401', function (): void {
    $this->getJson('/api/v1/auth/registrations')->assertUnauthorized();
});

test('authenticated user can retrieve all registrations', function (): void {
    Registration::query()->create([
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ]);

    Registration::query()->create([
        'name' => 'Bob',
        'email' => 'bob@example.com',
    ]);

    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->getJson('/api/v1/auth/registrations')->assertOk();

    $response->assertJsonCount(2);
    $response->assertJsonFragment([
        'name' => 'Alice',
        'email' => 'alice@example.com',
    ]);
    $response->assertJsonFragment([
        'name' => 'Bob',
        'email' => 'bob@example.com',
    ]);
});

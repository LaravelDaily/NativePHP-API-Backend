<?php

use App\Models\Cuesheet;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('unauthenticated request returns 401', function (): void {
    $this->getJson('/api/v1/auth/cuesheets')->assertUnauthorized();
});

test('authenticated user can retrieve all cuesheets', function (): void {
    Cuesheet::query()->create([
        'turn' => 'Left on Main St',
        'notes' => 'Turn at the light',
        'distance' => 1.2,
    ]);

    Cuesheet::query()->create([
        'turn' => 'Right on Oak Ave',
        'notes' => 'After the bridge',
        'distance' => 2.4,
    ]);

    Sanctum::actingAs(User::factory()->create(), ['*']);

    $response = $this->getJson('/api/v1/auth/cuesheets')->assertOk();

    $response->assertJsonCount(2);
    $response->assertJsonFragment([
        'turn' => 'Left on Main St',
        'notes' => 'Turn at the light',
        'distance' => 1.2,
    ]);
    $response->assertJsonFragment([
        'turn' => 'Right on Oak Ave',
        'notes' => 'After the bridge',
        'distance' => 2.4,
    ]);
});

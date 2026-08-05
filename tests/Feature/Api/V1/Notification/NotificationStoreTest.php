<?php

use App\Models\Notification;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

test('unauthenticated request returns 401', function (): void {
    $this->postJson('/api/v1/auth/notifications', [
        'DateSent' => '2026-08-05 10:30:00',
        'Message' => 'Hello world',
    ])->assertUnauthorized();
});

test('authenticated user can store a notification', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $this->postJson('/api/v1/auth/notifications', [
        'DateSent' => '2026-08-05 10:30:00',
        'Message' => 'Hello world',
    ])
        ->assertCreated()
        ->assertJsonStructure([
            'id',
            'DateSent',
            'Message',
            'created_at',
            'updated_at',
        ])
        ->assertJsonFragment([
            'Message' => 'Hello world',
        ]);

    $notification = Notification::query()->first();

    expect(Notification::query()->count())->toBe(1);
    expect($notification)->not->toBeNull();
    expect($notification?->Message)->toBe('Hello world');
});

test('missing required fields returns 422', function (): void {
    Sanctum::actingAs(User::factory()->create(), ['*']);

    $this->postJson('/api/v1/auth/notifications', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['DateSent', 'Message']);
});

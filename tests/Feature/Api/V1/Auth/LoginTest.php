<?php

use App\Models\User;

test('user can login with correct credentials and receives a token', function (): void {
    $user = User::factory()->create(['password' => 'password']);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => 'iPhone 15',
    ])
        ->assertSuccessful()
        ->assertJsonStructure(['token']);
});

test('login fails with wrong password', function (): void {
    $user = User::factory()->create(['password' => 'password']);

    $this->postJson('/api/v1/auth/login', [
        'email' => $user->email,
        'password' => 'wrong-password',
        'device_name' => 'iPhone 15',
    ])->assertUnprocessable()->assertJsonValidationErrors(['email']);
});

test('login fails when user does not exist', function (): void {
    $this->postJson('/api/v1/auth/login', [
        'email' => 'nobody@example.com',
        'password' => 'password',
        'device_name' => 'iPhone 15',
    ])->assertUnprocessable()->assertJsonValidationErrors(['email']);
});

test('login fails when required fields are missing', function (string $field): void {
    $data = [
        'email' => 'john@example.com',
        'password' => 'password',
        'device_name' => 'iPhone 15',
    ];

    unset($data[$field]);

    $this->postJson('/api/v1/auth/login', $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['email', 'password', 'device_name']);

<?php

use App\Models\User;

test('user can register with valid data and receives a token', function (): void {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'iPhone 15',
    ])
        ->assertCreated()
        ->assertJsonStructure(['token']);

    $this->assertDatabaseHas('users', ['email' => 'john@example.com']);
});

test('registration fails when email is already taken', function (): void {
    User::factory()->create(['email' => 'taken@example.com']);

    $this->postJson('/api/v1/auth/register', [
        'name' => 'John Doe',
        'email' => 'taken@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'iPhone 15',
    ])->assertUnprocessable()->assertJsonValidationErrors(['email']);
});

test('registration fails when password confirmation does not match', function (): void {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'different',
        'device_name' => 'iPhone 15',
    ])->assertUnprocessable()->assertJsonValidationErrors(['password']);
});

test('registration fails when password is fewer than 8 characters', function (): void {
    $this->postJson('/api/v1/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'short',
        'password_confirmation' => 'short',
        'device_name' => 'iPhone 15',
    ])->assertUnprocessable()->assertJsonValidationErrors(['password']);
});

test('registration fails when required fields are missing', function (string $field): void {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'device_name' => 'iPhone 15',
    ];

    unset($data[$field]);

    $this->postJson('/api/v1/auth/register', $data)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(['name', 'email', 'password', 'device_name']);

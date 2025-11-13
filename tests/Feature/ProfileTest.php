<?php

use App\Models\User;

test('profile page is displayed', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/profile');

    $response->assertOk();
});

test('profile information can be updated', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'department' => 'Computer Science',
            'school_year' => '2024-2025',
            'student_number' => '2024-00001',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('Test User', $user->name);
    $this->assertSame('test@example.com', $user->email);
    $this->assertSame('Computer Science', $user->department);
    $this->assertSame('2024-2025', $user->school_year);
    $this->assertSame('2024-00001', $user->student_number);
    $this->assertNull($user->email_verified_at);
});

test('email verification status is unchanged when the email address is unchanged', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => 'Test User',
            'email' => $user->email,
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $this->assertNotNull($user->refresh()->email_verified_at);
});

test('user can delete their account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->delete('/profile', [
            'password' => 'password',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/');

    $this->assertGuest();
    $this->assertNull($user->fresh());
});

test('correct password must be provided to delete account', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->from('/profile')
        ->delete('/profile', [
            'password' => 'wrong-password',
        ]);

    $response
        ->assertSessionHasErrors('password')
        ->assertRedirect('/profile');

    $this->assertNotNull($user->fresh());
});

test('school year can be updated', function () {
    $user = User::factory()->create([
        'school_year' => '2023-2024',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'school_year' => '2024-2025',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('2024-2025', $user->school_year);
});

test('student number can be updated', function () {
    $user = User::factory()->create([
        'role' => 'requisitioner',
        'student_number' => '2023-00001',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'student_number' => '2024-00001',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertSame('2024-00001', $user->student_number);
});

test('student number must be unique', function () {
    $existingUser = User::factory()->create([
        'student_number' => '2024-00001',
    ]);

    $user = User::factory()->create([
        'student_number' => '2024-00002',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'student_number' => '2024-00001', // Try to use existing student number
        ]);

    $response->assertSessionHasErrors('student_number');

    $user->refresh();

    $this->assertSame('2024-00002', $user->student_number);
});

test('school year and student number are optional', function () {
    $user = User::factory()->create([
        'school_year' => '2024-2025',
        'student_number' => '2024-00001',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'school_year' => '',
            'student_number' => '',
        ]);

    $response
        ->assertSessionHasNoErrors()
        ->assertRedirect('/profile');

    $user->refresh();

    $this->assertNull($user->school_year);
    $this->assertNull($user->student_number);
});

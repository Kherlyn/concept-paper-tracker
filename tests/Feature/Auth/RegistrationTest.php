<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('users can register with school year and student number', function () {
    $response = $this->post('/register', [
        'name' => 'Test Student',
        'email' => 'student@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
        'school_year' => '2024-2025',
        'student_number' => '2024-00001',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('users', [
        'email' => 'student@example.com',
        'school_year' => '2024-2025',
        'student_number' => '2024-00001',
    ]);
});

test('school year must follow acceptable format', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
        'school_year' => 'invalid format',
    ]);

    $response->assertSessionHasErrors('school_year');
});

test('school year accepts year range format', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test1@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
        'school_year' => '2024-2025',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
});

test('school year accepts year level format', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test2@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
        'school_year' => '1st Year',
    ]);

    $response->assertRedirect(route('dashboard', absolute: false));
});

test('student number must be unique', function () {
    // Create first user with student number
    \App\Models\User::factory()->create([
        'email' => 'first@example.com',
        'student_number' => '2024-00001',
    ]);

    // Attempt to register second user with same student number
    $response = $this->post('/register', [
        'name' => 'Second Student',
        'email' => 'second@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
        'student_number' => '2024-00001',
    ]);

    $response->assertSessionHasErrors('student_number');
});

test('users can register without optional fields', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'nofields@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));

    $this->assertDatabaseHas('users', [
        'email' => 'nofields@example.com',
        'school_year' => null,
        'student_number' => null,
    ]);
});

test('registration displays validation error for duplicate student number', function () {
    \App\Models\User::factory()->create([
        'student_number' => '2024-99999',
    ]);

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'duplicate@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
        'student_number' => '2024-99999',
    ]);

    $response->assertSessionHasErrors(['student_number']);
});

test('school year field accepts various valid formats', function () {
    $formats = ['2024-2025', '1st Year', '2nd Year', '3rd Year', '4th Year'];

    foreach ($formats as $index => $format) {
        // Logout any previously authenticated user
        auth()->guard('web')->logout();

        $response = $this->post('/register', [
            'name' => "Test User {$index}",
            'email' => "testformat{$index}@example.com",
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'requisitioner',
            'department' => 'Computer Science',
            'school_year' => $format,
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', [
            'email' => "testformat{$index}@example.com",
            'school_year' => $format,
        ]);
    }
});

test('school year field rejects invalid formats', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'invalidformat@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
        'role' => 'requisitioner',
        'department' => 'Computer Science',
        'school_year' => 'Invalid Format',
    ]);

    $response->assertSessionHasErrors('school_year');
});

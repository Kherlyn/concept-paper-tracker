<?php

use App\Models\User;

test('admin can view users list with academic fields', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'department' => 'Computer Science',
    'school_year' => '2024-2025',
    'student_number' => '2024-00001',
  ]);

  $response = $this
    ->actingAs($admin)
    ->get('/admin/users');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->component('Admin/Users')
      ->has('users.data', 2)
  );

  // Verify the user with academic fields exists in the response
  $this->assertDatabaseHas('users', [
    'email' => $user->email,
    'school_year' => '2024-2025',
    'student_number' => '2024-00001',
  ]);
});

test('admin can create user with school year and student number', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->post('/admin/users', [
      'name' => 'New Student',
      'email' => 'newstudent@example.com',
      'password' => 'password',
      'role' => 'requisitioner',
      'department' => 'Computer Science',
      'school_year' => '2024-2025',
      'student_number' => '2024-00100',
    ]);

  $response->assertRedirect();

  $this->assertDatabaseHas('users', [
    'email' => 'newstudent@example.com',
    'school_year' => '2024-2025',
    'student_number' => '2024-00100',
  ]);
});

test('admin can update user school year', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'department' => 'Computer Science',
    'school_year' => '2023-2024',
  ]);

  $response = $this
    ->actingAs($admin)
    ->put("/admin/users/{$user->id}", [
      'name' => $user->name,
      'email' => $user->email,
      'role' => $user->role,
      'department' => $user->department,
      'school_year' => '2024-2025',
    ]);

  $response->assertRedirect();

  $user->refresh();
  $this->assertSame('2024-2025', $user->school_year);
});

test('admin can update user student number', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'department' => 'Computer Science',
    'student_number' => '2023-00001',
  ]);

  $response = $this
    ->actingAs($admin)
    ->put("/admin/users/{$user->id}", [
      'name' => $user->name,
      'email' => $user->email,
      'role' => $user->role,
      'department' => $user->department,
      'student_number' => '2024-00001',
    ]);

  $response->assertRedirect();

  $user->refresh();
  $this->assertSame('2024-00001', $user->student_number);
});

test('non-admin cannot access user management', function () {
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'department' => 'Computer Science',
  ]);

  $response = $this
    ->actingAs($user)
    ->get('/admin/users');

  $response->assertStatus(403);
});

test('admin can filter users by school year', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);

  $user2024 = User::factory()->create([
    'role' => 'requisitioner',
    'department' => 'Computer Science',
    'school_year' => '2024-2025',
    'name' => 'Student 2024',
  ]);

  $user2023 = User::factory()->create([
    'role' => 'requisitioner',
    'department' => 'Computer Science',
    'school_year' => '2023-2024',
    'name' => 'Student 2023',
  ]);

  $response = $this
    ->actingAs($admin)
    ->get('/admin/users?school_year=2024-2025');

  $response->assertStatus(200);
  $response->assertInertia(
    fn($page) => $page
      ->component('Admin/Users')
      ->has('users.data', 1)
      ->where('users.data.0.school_year', '2024-2025')
      ->where('users.data.0.name', 'Student 2024')
  );
});

test('admin can create user without optional academic fields', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->post('/admin/users', [
      'name' => 'New User',
      'email' => 'newuser@example.com',
      'password' => 'password',
      'role' => 'sps',
      'department' => 'Administration',
    ]);

  $response->assertRedirect();

  $this->assertDatabaseHas('users', [
    'email' => 'newuser@example.com',
    'school_year' => null,
    'student_number' => null,
  ]);
});

test('admin cannot create user with duplicate student number', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);
  User::factory()->create([
    'role' => 'requisitioner',
    'department' => 'Computer Science',
    'student_number' => '2024-00001',
  ]);

  $response = $this
    ->actingAs($admin)
    ->post('/admin/users', [
      'name' => 'Duplicate Student',
      'email' => 'duplicate@example.com',
      'password' => 'password',
      'role' => 'requisitioner',
      'department' => 'Computer Science',
      'student_number' => '2024-00001',
    ]);

  $response->assertSessionHasErrors('student_number');
});

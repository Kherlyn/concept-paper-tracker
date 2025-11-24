<?php

use App\Models\User;
use App\Models\DeadlineOption;

test('authenticated user can get deadline options', function () {
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($user)
    ->getJson('/deadline-options');

  $response->assertStatus(200);
  $response->assertJsonStructure([
    'success',
    'deadline_options' => [
      '*' => [
        'key',
        'label',
        'days',
      ],
    ],
  ]);

  expect($response->json('deadline_options'))->toHaveCount(5);
});

test('admin can create new deadline option', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson('/admin/deadline-options', [
      'key' => '6_months',
      'label' => '6 Months',
      'days' => 180,
    ]);

  $response->assertStatus(201);
  $response->assertJson([
    'success' => true,
    'message' => 'Deadline option created successfully.',
    'deadline_option' => [
      'key' => '6_months',
      'label' => '6 Months',
      'days' => 180,
    ],
  ]);
});

test('admin cannot create deadline option with duplicate key', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson('/admin/deadline-options', [
      'key' => '1_week',
      'label' => 'One Week',
      'days' => 7,
    ]);

  $response->assertStatus(422);
  $response->assertJsonValidationErrors(['key']);
});

test('admin cannot create deadline option with negative days', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson('/admin/deadline-options', [
      'key' => 'negative',
      'label' => 'Negative',
      'days' => -5,
    ]);

  $response->assertStatus(422);
  $response->assertJsonValidationErrors(['days']);
});

test('admin cannot create deadline option with zero days', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson('/admin/deadline-options', [
      'key' => 'zero',
      'label' => 'Zero Days',
      'days' => 0,
    ]);

  $response->assertStatus(422);
  $response->assertJsonValidationErrors(['days']);
});

test('admin can update existing deadline option', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->putJson('/admin/deadline-options/1_week', [
      'label' => 'One Week Updated',
      'days' => 8,
    ]);

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
    'message' => 'Deadline option updated successfully.',
    'deadline_option' => [
      'key' => '1_week',
      'label' => 'One Week Updated',
      'days' => 8,
    ],
  ]);
});

test('admin cannot update non-existent deadline option', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->putJson('/admin/deadline-options/non_existent', [
      'label' => 'Non Existent',
      'days' => 10,
    ]);

  $response->assertStatus(404);
  $response->assertJson([
    'error' => 'Deadline option not found.',
  ]);
});

test('admin can delete deadline option', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->deleteJson('/admin/deadline-options/3_months');

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
    'message' => 'Deadline option deleted successfully.',
  ]);
});

test('admin cannot delete non-existent deadline option', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->deleteJson('/admin/deadline-options/non_existent');

  $response->assertStatus(404);
  $response->assertJson([
    'error' => 'Deadline option not found.',
  ]);
});

test('non-admin cannot create deadline option', function () {
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($user)
    ->postJson('/admin/deadline-options', [
      'key' => '6_months',
      'label' => '6 Months',
      'days' => 180,
    ]);

  $response->assertStatus(403);
});

test('non-admin cannot update deadline option', function () {
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($user)
    ->putJson('/admin/deadline-options/1_week', [
      'label' => 'Updated',
      'days' => 10,
    ]);

  $response->assertStatus(403);
});

test('non-admin cannot delete deadline option', function () {
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($user)
    ->deleteJson('/admin/deadline-options/1_week');

  $response->assertStatus(403);
});

test('existing concept papers preserve deadline selections after config update', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);
  $requisitioner = User::factory()->create([
    'role' => 'requisitioner',
    'is_active' => true,
  ]);

  // Create a concept paper with a deadline
  $paper = \App\Models\ConceptPaper::factory()->create([
    'requisitioner_id' => $requisitioner->id,
    'deadline_option' => '1_month',
    'deadline_date' => now()->addDays(30),
  ]);

  $originalDeadline = $paper->deadline_date;

  // Admin updates the deadline option
  $this
    ->actingAs($admin)
    ->putJson('/admin/deadline-options/1_month', [
      'label' => '1 Month Updated',
      'days' => 45, // Changed from 30 to 45
    ]);

  // Refresh the paper from database
  $paper->refresh();

  // The deadline_date should remain unchanged
  expect($paper->deadline_date->toDateTimeString())
    ->toBe($originalDeadline->toDateTimeString());
});

test('deadline option key must be alphanumeric with underscores', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson('/admin/deadline-options', [
      'key' => 'invalid-key-with-dashes',
      'label' => 'Invalid Key',
      'days' => 30,
    ]);

  $response->assertStatus(422);
  $response->assertJsonValidationErrors(['key']);
});

test('deadline option days cannot exceed 365', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson('/admin/deadline-options', [
      'key' => 'too_long',
      'label' => 'Too Long',
      'days' => 400,
    ]);

  $response->assertStatus(422);
  $response->assertJsonValidationErrors(['days']);
});

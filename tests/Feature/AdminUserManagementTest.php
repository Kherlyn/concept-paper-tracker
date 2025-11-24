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

test('admin can toggle user activation status', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'is_active' => true,
  ]);

  // Deactivate user
  $response = $this
    ->actingAs($admin)
    ->postJson("/admin/users/{$user->id}/toggle-activation");

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
    'message' => 'User deactivated successfully.',
  ]);

  $user->refresh();
  expect($user->is_active)->toBeFalse();
  expect($user->deactivated_at)->not->toBeNull();
  expect($user->deactivated_by)->toBe($admin->id);

  // Reactivate user
  $response = $this
    ->actingAs($admin)
    ->postJson("/admin/users/{$user->id}/toggle-activation");

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
    'message' => 'User activated successfully.',
  ]);

  $user->refresh();
  expect($user->is_active)->toBeTrue();
  expect($user->deactivated_at)->toBeNull();
  expect($user->deactivated_by)->toBeNull();
});

test('admin cannot deactivate themselves', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'department' => 'Administration',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson("/admin/users/{$admin->id}/toggle-activation");

  $response->assertStatus(422);
  $response->assertJson([
    'error' => 'You cannot deactivate your own account.',
  ]);

  $admin->refresh();
  expect($admin->is_active)->toBeTrue();
});

test('non-admin cannot toggle user activation', function () {
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'is_active' => true,
  ]);
  $targetUser = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);

  $response = $this
    ->actingAs($user)
    ->postJson("/admin/users/{$targetUser->id}/toggle-activation");

  $response->assertStatus(403);
});

test('deactivating user returns affected concept papers', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);
  $user = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);

  // Create concept papers with stages assigned to the user
  $paper1 = \App\Models\ConceptPaper::factory()->create([
    'title' => 'Test Paper 1',
    'tracking_number' => 'CP-2024-001',
  ]);
  $paper2 = \App\Models\ConceptPaper::factory()->create([
    'title' => 'Test Paper 2',
    'tracking_number' => 'CP-2024-002',
  ]);

  $stage1 = \App\Models\WorkflowStage::factory()->create([
    'concept_paper_id' => $paper1->id,
    'assigned_user_id' => $user->id,
    'assigned_role' => 'sps',
    'status' => 'in_progress',
    'stage_name' => 'SPS Review',
    'stage_order' => 1,
  ]);
  $stage2 = \App\Models\WorkflowStage::factory()->create([
    'concept_paper_id' => $paper2->id,
    'assigned_user_id' => $user->id,
    'assigned_role' => 'sps',
    'status' => 'pending',
    'stage_name' => 'SPS Review',
    'stage_order' => 1,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson("/admin/users/{$user->id}/toggle-activation");

  $response->assertStatus(200);
  $response->assertJsonStructure([
    'success',
    'message',
    'user',
    'affected_papers' => [
      '*' => [
        'id',
        'title',
        'tracking_number',
        'requisitioner',
        'stages',
      ],
    ],
  ]);

  expect($response->json('affected_papers'))->toHaveCount(2);
});

test('admin can get assigned stages for a user', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);
  $user = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);

  $paper = \App\Models\ConceptPaper::factory()->create([
    'title' => 'Test Paper',
    'tracking_number' => 'CP-2024-001',
  ]);

  $stage = \App\Models\WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_user_id' => $user->id,
    'assigned_role' => 'sps',
    'status' => 'in_progress',
    'stage_name' => 'SPS Review',
    'stage_order' => 1,
  ]);

  $response = $this
    ->actingAs($admin)
    ->getJson("/admin/users/{$user->id}/assigned-stages");

  $response->assertStatus(200);
  $response->assertJsonStructure([
    'success',
    'affected_papers' => [
      '*' => [
        'id',
        'title',
        'tracking_number',
        'requisitioner',
        'stages',
      ],
    ],
  ]);

  expect($response->json('affected_papers'))->toHaveCount(1);
});

test('admin can reassign workflow stage to another user', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);
  $oldUser = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);
  $newUser = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);

  $paper = \App\Models\ConceptPaper::factory()->create([
    'title' => 'Test Paper',
    'tracking_number' => 'CP-2024-001',
  ]);

  $stage = \App\Models\WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_user_id' => $oldUser->id,
    'assigned_role' => 'sps',
    'status' => 'in_progress',
    'stage_name' => 'SPS Review',
    'stage_order' => 1,
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson("/admin/workflow-stages/{$stage->id}/reassign", [
      'new_user_id' => $newUser->id,
    ]);

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
    'message' => 'Stage reassigned successfully.',
  ]);

  $stage->refresh();
  expect($stage->assigned_user_id)->toBe($newUser->id);

  // Check audit log was created
  $this->assertDatabaseHas('audit_logs', [
    'concept_paper_id' => $paper->id,
    'user_id' => $admin->id,
    'action' => 'stage_reassigned',
    'stage_name' => 'SPS Review',
  ]);
});

test('admin cannot reassign stage to inactive user', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);
  $oldUser = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);
  $inactiveUser = User::factory()->create([
    'role' => 'sps',
    'is_active' => false,
  ]);

  $paper = \App\Models\ConceptPaper::factory()->create();
  $stage = \App\Models\WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_user_id' => $oldUser->id,
    'assigned_role' => 'sps',
    'status' => 'in_progress',
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson("/admin/workflow-stages/{$stage->id}/reassign", [
      'new_user_id' => $inactiveUser->id,
    ]);

  $response->assertStatus(422);
  $response->assertJson([
    'error' => 'Cannot reassign to an inactive user.',
  ]);

  $stage->refresh();
  expect($stage->assigned_user_id)->toBe($oldUser->id);
});

test('admin cannot reassign stage to user with wrong role', function () {
  $admin = User::factory()->create([
    'role' => 'admin',
    'is_active' => true,
  ]);
  $oldUser = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);
  $wrongRoleUser = User::factory()->create([
    'role' => 'vp_acad',
    'is_active' => true,
  ]);

  $paper = \App\Models\ConceptPaper::factory()->create();
  $stage = \App\Models\WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_user_id' => $oldUser->id,
    'assigned_role' => 'sps',
    'status' => 'in_progress',
  ]);

  $response = $this
    ->actingAs($admin)
    ->postJson("/admin/workflow-stages/{$stage->id}/reassign", [
      'new_user_id' => $wrongRoleUser->id,
    ]);

  $response->assertStatus(422);
  $response->assertJsonFragment([
    'error' => 'The selected user does not have the required role (sps).',
  ]);

  $stage->refresh();
  expect($stage->assigned_user_id)->toBe($oldUser->id);
});

test('non-admin cannot reassign workflow stages', function () {
  $user = User::factory()->create([
    'role' => 'requisitioner',
    'is_active' => true,
  ]);
  $spsUser = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);
  $newUser = User::factory()->create([
    'role' => 'sps',
    'is_active' => true,
  ]);

  $paper = \App\Models\ConceptPaper::factory()->create();
  $stage = \App\Models\WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_user_id' => $spsUser->id,
    'assigned_role' => 'sps',
    'status' => 'in_progress',
  ]);

  $response = $this
    ->actingAs($user)
    ->postJson("/admin/workflow-stages/{$stage->id}/reassign", [
      'new_user_id' => $newUser->id,
    ]);

  $response->assertStatus(403);
});

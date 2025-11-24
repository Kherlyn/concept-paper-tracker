<?php

use App\Models\Annotation;
use App\Models\ConceptPaper;
use App\Models\DeadlineOption;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// AnnotationPolicy Tests
test('active user can create annotation on viewable concept paper', function () {
  $requisitioner = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $requisitioner->id]);

  expect($requisitioner->can('annotate', $conceptPaper))->toBeTrue();
});

test('inactive user cannot create annotation', function () {
  $requisitioner = User::factory()->create(['role' => 'requisitioner', 'is_active' => false]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $requisitioner->id]);

  expect($requisitioner->can('annotate', $conceptPaper))->toBeFalse();
});

test('user can update their own annotation', function () {
  $user = User::factory()->create(['is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create();
  $annotation = Annotation::factory()->create([
    'user_id' => $user->id,
    'concept_paper_id' => $conceptPaper->id,
  ]);

  expect($user->can('update', $annotation))->toBeTrue();
});

test('user cannot update another users annotation', function () {
  $user1 = User::factory()->create(['is_active' => true]);
  $user2 = User::factory()->create(['is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create();
  $annotation = Annotation::factory()->create([
    'user_id' => $user1->id,
    'concept_paper_id' => $conceptPaper->id,
  ]);

  expect($user2->can('update', $annotation))->toBeFalse();
});

test('admin can update any annotation', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $user = User::factory()->create(['is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create();
  $annotation = Annotation::factory()->create([
    'user_id' => $user->id,
    'concept_paper_id' => $conceptPaper->id,
  ]);

  expect($admin->can('update', $annotation))->toBeTrue();
});

// UserPolicy Tests
test('admin can manage user activation', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

  expect($admin->can('manageActivation', User::class))->toBeTrue();
});

test('non-admin cannot manage user activation', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  expect($user->can('manageActivation', User::class))->toBeFalse();
});

test('admin can toggle activation of other users', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $targetUser = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  expect($admin->can('toggleActivation', $targetUser))->toBeTrue();
});

test('admin cannot toggle their own activation', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

  expect($admin->can('toggleActivation', $admin))->toBeFalse();
});

test('inactive admin cannot toggle user activation', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => false]);
  $targetUser = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  expect($admin->can('toggleActivation', $targetUser))->toBeFalse();
});

// DeadlineOptionPolicy Tests
test('admin can create deadline options', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

  expect($admin->can('create', DeadlineOption::class))->toBeTrue();
});

test('non-admin cannot create deadline options', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  expect($user->can('create', DeadlineOption::class))->toBeFalse();
});

test('admin can update deadline options', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $deadlineOption = DeadlineOption::factory()->create();

  expect($admin->can('update', $deadlineOption))->toBeTrue();
});

test('non-admin cannot update deadline options', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $deadlineOption = DeadlineOption::factory()->create();

  expect($user->can('update', $deadlineOption))->toBeFalse();
});

test('admin can delete deadline options', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $deadlineOption = DeadlineOption::factory()->create();

  expect($admin->can('delete', $deadlineOption))->toBeTrue();
});

test('non-admin cannot delete deadline options', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $deadlineOption = DeadlineOption::factory()->create();

  expect($user->can('delete', $deadlineOption))->toBeFalse();
});

// WorkflowStagePolicy Tests
test('admin can reassign workflow stages', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $stage = WorkflowStage::factory()->create(['status' => 'pending']);

  expect($admin->can('reassign', $stage))->toBeTrue();
});

test('non-admin cannot reassign workflow stages', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $stage = WorkflowStage::factory()->create(['status' => 'pending']);

  expect($user->can('reassign', $stage))->toBeFalse();
});

test('admin cannot reassign completed stages', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $stage = WorkflowStage::factory()->create(['status' => 'completed']);

  expect($admin->can('reassign', $stage))->toBeFalse();
});

test('inactive admin cannot reassign stages', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => false]);
  $stage = WorkflowStage::factory()->create(['status' => 'pending']);

  expect($admin->can('reassign', $stage))->toBeFalse();
});

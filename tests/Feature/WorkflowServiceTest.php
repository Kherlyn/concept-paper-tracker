<?php

use App\Models\ConceptPaper;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Services\WorkflowService;

test('createStagesForPaper creates all stages when students are involved', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => true,
  ]);

  $workflowService = new WorkflowService();
  $workflowService->createStagesForPaper($paper);

  // Should create all 10 stages including SPS Review
  expect($paper->stages()->count())->toBe(10);
  expect($paper->stages()->where('stage_name', 'SPS Review')->exists())->toBeTrue();
  expect($paper->stages()->where('stage_name', 'VP Acad Review')->exists())->toBeTrue();
  expect($paper->stages()->where('stage_name', 'Senior VP Approval')->exists())->toBeTrue();
});

test('createStagesForPaper skips SPS stage when students are not involved', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => false,
  ]);

  $workflowService = new WorkflowService();
  $workflowService->createStagesForPaper($paper);

  // Should create 9 stages (10 - 1 skipped SPS)
  expect($paper->stages()->count())->toBe(9);
  expect($paper->stages()->where('stage_name', 'SPS Review')->exists())->toBeFalse();
  expect($paper->stages()->where('stage_name', 'VP Acad Review')->exists())->toBeTrue();
});

test('createStagesForPaper logs skipped stage in audit trail', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => false,
  ]);

  $workflowService = new WorkflowService();
  $workflowService->createStagesForPaper($paper);

  // Check audit log for skipped stage
  $auditLog = $paper->auditLogs()->where('action', 'stage_skipped')->first();

  expect($auditLog)->not->toBeNull();
  expect($auditLog->stage_name)->toBe('SPS Review');
  expect($auditLog->remarks)->toContain('no student involvement');
  expect($auditLog->metadata['reason'])->toBe('students_involved_false');
});

test('shouldSkipStage returns true for SPS when students not involved', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => false,
  ]);

  $workflowService = new WorkflowService();

  expect($workflowService->shouldSkipStage($paper, 'SPS Review'))->toBeTrue();
  expect($workflowService->shouldSkipStage($paper, 'VP Acad Review'))->toBeFalse();
});

test('shouldSkipStage returns false for SPS when students are involved', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => true,
  ]);

  $workflowService = new WorkflowService();

  expect($workflowService->shouldSkipStage($paper, 'SPS Review'))->toBeFalse();
});

test('getNextStage returns correct next stage information', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => true,
  ]);

  $workflowService = new WorkflowService();
  $workflowService->createStagesForPaper($paper);

  $firstStage = $paper->stages()->where('stage_order', 1)->first();
  $nextStageInfo = $workflowService->getNextStage($firstStage);

  expect($nextStageInfo)->not->toBeNull();
  expect($nextStageInfo['name'])->toBe('VP Acad Review');
  expect($nextStageInfo['order'])->toBe(2);
});

test('getNextStage returns null when no next stage exists', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => true,
  ]);

  $workflowService = new WorkflowService();
  $workflowService->createStagesForPaper($paper);

  // Get the actual last stage (Budget Release should be stage 10)
  $lastStage = $paper->stages()->where('stage_name', 'Budget Release')->first();
  $nextStageInfo = $workflowService->getNextStage($lastStage);

  expect($nextStageInfo)->toBeNull();
});

test('reassignStage updates stage assignment and creates audit log', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $oldUser = User::factory()->create(['role' => 'vp_acad', 'is_active' => true]);
  $newUser = User::factory()->create(['role' => 'vp_acad', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $admin->id,
    'students_involved' => true,
  ]);

  $workflowService = new WorkflowService();
  $workflowService->createStagesForPaper($paper);

  $stage = $paper->stages()->where('stage_name', 'VP Acad Review')->first();
  $stage->assigned_user_id = $oldUser->id;
  $stage->save();

  $workflowService->reassignStage($stage, $newUser, $admin);

  // Check stage was reassigned
  expect($stage->fresh()->assigned_user_id)->toBe($newUser->id);

  // Check audit log was created
  $auditLog = $paper->auditLogs()->where('action', 'stage_reassigned')->first();

  expect($auditLog)->not->toBeNull();
  expect($auditLog->user_id)->toBe($admin->id);
  expect($auditLog->stage_name)->toBe('VP Acad Review');
  expect($auditLog->metadata['old_user_id'])->toBe($oldUser->id);
  expect($auditLog->metadata['new_user_id'])->toBe($newUser->id);
  expect($auditLog->metadata['admin_id'])->toBe($admin->id);
});

test('workflow includes Senior VP Approval stage after Auditing Review', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => true,
  ]);

  $workflowService = new WorkflowService();
  $workflowService->createStagesForPaper($paper);

  $auditingStage = $paper->stages()->where('stage_name', 'Auditing Review')->first();
  $seniorVpStage = $paper->stages()->where('stage_name', 'Senior VP Approval')->first();
  $acadCopyStage = $paper->stages()->where('stage_name', 'Acad Copy Distribution')->first();

  // Verify Senior VP Approval comes after Auditing Review
  expect($seniorVpStage->stage_order)->toBe($auditingStage->stage_order + 1);

  // Verify Acad Copy Distribution comes after Senior VP Approval
  expect($acadCopyStage->stage_order)->toBe($seniorVpStage->stage_order + 1);
});

test('first stage is VP Acad Review when SPS is skipped', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => false,
  ]);

  $workflowService = new WorkflowService();
  $workflowService->createStagesForPaper($paper);

  $firstStage = $paper->stages()->where('stage_order', 1)->first();

  // When SPS is skipped, VP Acad Review should be the first stage
  expect($firstStage->stage_name)->toBe('VP Acad Review');
  expect($firstStage->status)->toBe('in_progress');
});

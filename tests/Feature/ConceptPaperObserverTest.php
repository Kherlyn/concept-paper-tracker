<?php

use App\Models\ConceptPaper;
use App\Models\AuditLog;
use App\Models\User;

test('observer logs concept paper creation with submitted action', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);

  $this->actingAs($user);

  $conceptPaper = ConceptPaper::create([
    'requisitioner_id' => $user->id,
    'department' => 'Computer Science',
    'title' => 'Test Concept Paper',
    'nature_of_request' => 'regular',
  ]);

  $this->assertDatabaseHas('audit_logs', [
    'concept_paper_id' => $conceptPaper->id,
    'user_id' => $user->id,
    'action' => 'submitted',
  ]);

  $auditLog = AuditLog::where('concept_paper_id', $conceptPaper->id)
    ->where('action', 'submitted')
    ->first();

  expect($auditLog)->not->toBeNull();
  expect($auditLog->metadata)->toHaveKey('tracking_number');
  expect($auditLog->metadata)->toHaveKey('title');
  expect($auditLog->metadata['title'])->toBe('Test Concept Paper');
});

test('observer logs concept paper updates with changed fields', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);

  $this->actingAs($user);

  $conceptPaper = ConceptPaper::create([
    'requisitioner_id' => $user->id,
    'department' => 'Computer Science',
    'title' => 'Original Title',
    'nature_of_request' => 'regular',
  ]);

  // Update the concept paper
  $conceptPaper->update([
    'title' => 'Updated Title',
    'nature_of_request' => 'urgent',
  ]);

  // Check that an update audit log was created
  $this->assertDatabaseHas('audit_logs', [
    'concept_paper_id' => $conceptPaper->id,
    'action' => 'updated',
  ]);

  $auditLog = AuditLog::where('concept_paper_id', $conceptPaper->id)
    ->where('action', 'updated')
    ->first();

  expect($auditLog)->not->toBeNull();
  expect($auditLog->metadata)->toHaveKey('changes');
  expect($auditLog->metadata)->toHaveKey('original');
  expect($auditLog->metadata['changes'])->toHaveKey('title');
  expect($auditLog->metadata['changes']['title'])->toBe('Updated Title');
  expect($auditLog->metadata['original']['title'])->toBe('Original Title');
});

test('observer logs concept paper deletion', function () {
  $user = User::factory()->create(['role' => 'admin']);

  $this->actingAs($user);

  $conceptPaper = ConceptPaper::create([
    'requisitioner_id' => $user->id,
    'department' => 'Computer Science',
    'title' => 'Test Concept Paper',
    'nature_of_request' => 'regular',
  ]);

  $conceptPaperId = $conceptPaper->id;
  $trackingNumber = $conceptPaper->tracking_number;

  // Delete the concept paper
  $conceptPaper->delete();

  // Check that a deletion audit log was created
  $this->assertDatabaseHas('audit_logs', [
    'concept_paper_id' => $conceptPaperId,
    'user_id' => $user->id,
    'action' => 'deleted',
  ]);

  $auditLog = AuditLog::where('concept_paper_id', $conceptPaperId)
    ->where('action', 'deleted')
    ->first();

  expect($auditLog)->not->toBeNull();
  expect($auditLog->metadata)->toHaveKey('tracking_number');
  expect($auditLog->metadata['tracking_number'])->toBe($trackingNumber);
});

test('observer does not log updates when only timestamps change', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);

  $this->actingAs($user);

  $conceptPaper = ConceptPaper::create([
    'requisitioner_id' => $user->id,
    'department' => 'Computer Science',
    'title' => 'Test Concept Paper',
    'nature_of_request' => 'regular',
  ]);

  // Count audit logs after creation
  $initialLogCount = AuditLog::where('concept_paper_id', $conceptPaper->id)->count();

  // Touch the model (only updates timestamps)
  $conceptPaper->touch();

  // Count audit logs after touch
  $finalLogCount = AuditLog::where('concept_paper_id', $conceptPaper->id)->count();

  // Should not create a new audit log for timestamp-only changes
  expect($finalLogCount)->toBe($initialLogCount);
});

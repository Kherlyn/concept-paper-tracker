<?php

use App\Models\ConceptPaper;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Notifications\PaperCompletedNotification;
use App\Notifications\PaperReturnedNotification;
use App\Notifications\StageAssignedNotification;
use App\Notifications\StageOverdueNotification;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
  Notification::fake();
});

test('stage assigned notification sends email', function () {
  $requisitioner = User::factory()->create(['role' => 'requisitioner']);
  $sps = User::factory()->create(['role' => 'sps']);

  $conceptPaper = ConceptPaper::factory()->create([
    'requisitioner_id' => $requisitioner->id,
  ]);

  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'assigned_user_id' => $sps->id,
    'stage_name' => 'SPS Review',
  ]);

  $sps->notify(new StageAssignedNotification($stage));

  Notification::assertSentTo($sps, StageAssignedNotification::class);
});

test('stage overdue notification sends email', function () {
  $sps = User::factory()->create(['role' => 'sps']);
  $conceptPaper = ConceptPaper::factory()->create();

  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'assigned_user_id' => $sps->id,
    'deadline' => now()->subDay(),
  ]);

  $sps->notify(new StageOverdueNotification($stage));

  Notification::assertSentTo($sps, StageOverdueNotification::class);
});

test('paper completed notification sends email', function () {
  $requisitioner = User::factory()->create(['role' => 'requisitioner']);

  $conceptPaper = ConceptPaper::factory()->create([
    'requisitioner_id' => $requisitioner->id,
    'status' => 'completed',
    'completed_at' => now(),
  ]);

  $requisitioner->notify(new PaperCompletedNotification($conceptPaper));

  Notification::assertSentTo($requisitioner, PaperCompletedNotification::class);
});

test('paper returned notification sends email', function () {
  $requisitioner = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create([
    'requisitioner_id' => $requisitioner->id,
  ]);

  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'stage_name' => 'SPS Review',
  ]);

  $remarks = 'Please provide more details about the budget allocation.';

  $requisitioner->notify(new PaperReturnedNotification($stage, $remarks));

  Notification::assertSentTo($requisitioner, PaperReturnedNotification::class);
});

test('notifications include mail channel', function () {
  $user = User::factory()->create();
  $conceptPaper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
  ]);

  $notification = new StageAssignedNotification($stage);

  expect($notification->via($user))->toContain('mail');
});

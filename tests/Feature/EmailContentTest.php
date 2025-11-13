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

test('stage assigned email contains correct content', function () {
  $sps = User::factory()->create([
    'role' => 'sps',
    'name' => 'John Doe',
    'email' => 'sps@example.com',
  ]);

  $conceptPaper = ConceptPaper::factory()->create([
    'title' => 'Test Concept Paper',
    'tracking_number' => 'CP-2025-TEST-001',
  ]);

  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'assigned_user_id' => $sps->id,
    'stage_name' => 'SPS Review',
    'deadline' => now()->addDays(1),
  ]);

  $sps->notify(new StageAssignedNotification($stage));

  Notification::assertSentTo($sps, StageAssignedNotification::class, function ($notification) use ($sps, $conceptPaper, $stage) {
    $mailMessage = $notification->toMail($sps);

    // Check subject
    expect($mailMessage->subject)->toBe('New Stage Assignment: SPS Review');

    // Check markdown view
    expect($mailMessage->markdown)->toBe('mail.stage-assigned');

    // Check view data
    expect($mailMessage->viewData)->toHaveKey('notifiable');
    expect($mailMessage->viewData)->toHaveKey('stage');
    expect($mailMessage->viewData)->toHaveKey('conceptPaper');
    expect($mailMessage->viewData['conceptPaper']->title)->toBe('Test Concept Paper');
    expect($mailMessage->viewData['stage']->stage_name)->toBe('SPS Review');

    return true;
  });
});

test('stage overdue email contains correct content', function () {
  $sps = User::factory()->create([
    'role' => 'sps',
    'name' => 'Jane Smith',
  ]);

  $conceptPaper = ConceptPaper::factory()->create([
    'title' => 'Overdue Paper',
    'tracking_number' => 'CP-2025-OVERDUE-001',
  ]);

  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'assigned_user_id' => $sps->id,
    'stage_name' => 'VP Acad Review',
    'deadline' => now()->subDays(2),
  ]);

  $sps->notify(new StageOverdueNotification($stage));

  Notification::assertSentTo($sps, StageOverdueNotification::class, function ($notification) use ($sps) {
    $mailMessage = $notification->toMail($sps);

    expect($mailMessage->subject)->toBe('Overdue Stage Alert: VP Acad Review');
    expect($mailMessage->markdown)->toBe('mail.stage-overdue');

    return true;
  });
});

test('paper completed email contains correct content', function () {
  $requisitioner = User::factory()->create([
    'role' => 'requisitioner',
    'name' => 'Bob Johnson',
  ]);

  $conceptPaper = ConceptPaper::factory()->create([
    'requisitioner_id' => $requisitioner->id,
    'title' => 'Completed Paper',
    'tracking_number' => 'CP-2025-DONE-001',
    'status' => 'completed',
    'completed_at' => now(),
  ]);

  $requisitioner->notify(new PaperCompletedNotification($conceptPaper));

  Notification::assertSentTo($requisitioner, PaperCompletedNotification::class, function ($notification) use ($requisitioner) {
    $mailMessage = $notification->toMail($requisitioner);

    expect($mailMessage->subject)->toBe('Concept Paper Completed: Completed Paper');
    expect($mailMessage->markdown)->toBe('mail.paper-completed');
    expect($mailMessage->viewData)->toHaveKey('conceptPaper');

    return true;
  });
});

test('paper returned email contains remarks', function () {
  $requisitioner = User::factory()->create(['role' => 'requisitioner']);

  $conceptPaper = ConceptPaper::factory()->create([
    'requisitioner_id' => $requisitioner->id,
    'title' => 'Returned Paper',
  ]);

  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'stage_name' => 'Auditing Review',
  ]);

  $remarks = 'Budget justification is insufficient. Please provide detailed breakdown.';

  $requisitioner->notify(new PaperReturnedNotification($stage, $remarks));

  Notification::assertSentTo($requisitioner, PaperReturnedNotification::class, function ($notification) use ($requisitioner, $remarks) {
    $mailMessage = $notification->toMail($requisitioner);

    expect($mailMessage->subject)->toContain('Concept Paper Returned');
    expect($mailMessage->markdown)->toBe('mail.paper-returned');
    expect($mailMessage->viewData)->toHaveKey('remarks');
    expect($mailMessage->viewData['remarks'])->toBe($remarks);

    return true;
  });
});

test('all notifications implement ShouldQueue interface', function () {
  expect(StageAssignedNotification::class)->toImplement(\Illuminate\Contracts\Queue\ShouldQueue::class);
  expect(StageOverdueNotification::class)->toImplement(\Illuminate\Contracts\Queue\ShouldQueue::class);
  expect(PaperCompletedNotification::class)->toImplement(\Illuminate\Contracts\Queue\ShouldQueue::class);
  expect(PaperReturnedNotification::class)->toImplement(\Illuminate\Contracts\Queue\ShouldQueue::class);
});

test('all notifications include both database and mail channels', function () {
  $user = User::factory()->create();
  $conceptPaper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
  ]);

  $notifications = [
    new StageAssignedNotification($stage),
    new StageOverdueNotification($stage),
    new PaperCompletedNotification($conceptPaper),
    new PaperReturnedNotification($stage, 'Test remarks'),
  ];

  foreach ($notifications as $notification) {
    $channels = $notification->via($user);
    expect($channels)->toContain('database');
    expect($channels)->toContain('mail');
  }
});

<?php

use App\Jobs\CheckDeadlinesJob;
use App\Jobs\ConvertDocumentJob;
use App\Jobs\SendApprovalNotificationJob;
use App\Jobs\SendDeadlineNotificationJob;
use App\Models\Attachment;
use App\Models\ConceptPaper;
use App\Models\User;
use App\Services\DocumentPreviewService;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

test('ConvertDocumentJob can be dispatched', function () {
  Queue::fake();

  $user = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $attachment = Attachment::create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
    'file_name' => 'test.docx',
    'file_path' => 'test/path.docx',
    'file_size' => 1024,
    'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'uploaded_by' => $user->id,
  ]);

  ConvertDocumentJob::dispatch($attachment);

  Queue::assertPushed(ConvertDocumentJob::class);
});

test('SendDeadlineNotificationJob can be dispatched', function () {
  Queue::fake();

  $user = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => now()->subDay(),
    'status' => 'pending',
  ]);

  SendDeadlineNotificationJob::dispatch($paper);

  Queue::assertPushed(SendDeadlineNotificationJob::class);
});

test('SendApprovalNotificationJob can be dispatched', function () {
  Queue::fake();

  $user = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'status' => 'completed',
  ]);

  SendApprovalNotificationJob::dispatch($paper);

  Queue::assertPushed(SendApprovalNotificationJob::class);
});

test('CheckDeadlinesJob identifies papers with reached deadlines', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);

  // Create a paper with deadline reached
  $paperWithDeadline = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => now()->subDay(),
    'status' => 'pending',
  ]);

  // Create a paper without deadline
  $paperWithoutDeadline = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => null,
    'status' => 'pending',
  ]);

  // Create a completed paper with deadline
  $completedPaper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => now()->subDay(),
    'status' => 'completed',
  ]);

  Queue::fake();

  $job = new CheckDeadlinesJob();
  $job->handle();

  // Should only dispatch notification for the paper with reached deadline that's not completed
  Queue::assertPushed(SendDeadlineNotificationJob::class, 1);
});

test('CheckDeadlinesJob prevents duplicate notifications using cache', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => now()->subDay(),
    'status' => 'pending',
  ]);

  Queue::fake();

  // First run - should dispatch notification
  $job1 = new CheckDeadlinesJob();
  $job1->handle();

  Queue::assertPushed(SendDeadlineNotificationJob::class, 1);

  // Second run - should not dispatch again due to cache
  $job2 = new CheckDeadlinesJob();
  $job2->handle();

  // Still only 1 notification dispatched
  Queue::assertPushed(SendDeadlineNotificationJob::class, 1);
});

test('SendDeadlineNotificationJob skips completed papers', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => now()->subDay(),
    'status' => 'completed',
  ]);

  $notificationService = \Mockery::mock(NotificationService::class);
  $notificationService->shouldNotReceive('sendDeadlineReachedNotification');

  $job = new SendDeadlineNotificationJob($paper);
  $job->handle($notificationService);
});

test('jobs have retry logic with exponential backoff', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $attachment = Attachment::create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
    'file_name' => 'test.docx',
    'file_path' => 'test/path.docx',
    'file_size' => 1024,
    'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'uploaded_by' => $user->id,
  ]);

  $convertJob = new ConvertDocumentJob($attachment);
  expect($convertJob->tries)->toBe(3);
  expect($convertJob->backoff)->toBe([60, 300, 900]);

  $deadlineJob = new SendDeadlineNotificationJob($paper);
  expect($deadlineJob->tries)->toBe(3);
  expect($deadlineJob->backoff)->toBe([60, 300, 900]);

  $approvalJob = new SendApprovalNotificationJob($paper);
  expect($approvalJob->tries)->toBe(3);
  expect($approvalJob->backoff)->toBe([60, 300, 900]);

  $checkJob = new CheckDeadlinesJob();
  expect($checkJob->tries)->toBe(3);
  expect($checkJob->backoff)->toBe([60, 300, 900]);
});

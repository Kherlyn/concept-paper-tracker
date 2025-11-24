<?php

use App\Models\ConceptPaper;
use App\Models\User;
use Carbon\Carbon;

test('concept paper can have students_involved field', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'students_involved' => true,
  ]);

  expect($paper->students_involved)->toBeTrue();

  $paper->students_involved = false;
  $paper->save();

  expect($paper->fresh()->students_involved)->toBeFalse();
});

test('concept paper can have deadline_option and deadline_date fields', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $deadlineDate = Carbon::now()->addWeeks(2);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_option' => '2_weeks',
    'deadline_date' => $deadlineDate,
  ]);

  expect($paper->deadline_option)->toBe('2_weeks');
  expect($paper->deadline_date)->toBeInstanceOf(Carbon::class);
  expect($paper->deadline_date->format('Y-m-d'))->toBe($deadlineDate->format('Y-m-d'));
});

test('isDeadlineReached returns false when deadline is in future', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => Carbon::now()->addDays(5),
    'status' => 'pending',
  ]);

  expect($paper->isDeadlineReached())->toBeFalse();
});

test('isDeadlineReached returns true when deadline is in past and not completed', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => Carbon::now()->subDays(1),
    'status' => 'pending',
  ]);

  expect($paper->isDeadlineReached())->toBeTrue();
});

test('isDeadlineReached returns false when paper is completed', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => Carbon::now()->subDays(1),
    'status' => 'completed',
  ]);

  expect($paper->isDeadlineReached())->toBeFalse();
});

test('isDeadlineReached returns false when no deadline is set', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $user->id,
    'deadline_date' => null,
    'status' => 'pending',
  ]);

  expect($paper->isDeadlineReached())->toBeFalse();
});

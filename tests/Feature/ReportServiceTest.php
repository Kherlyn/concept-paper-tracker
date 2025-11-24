<?php

use App\Models\ConceptPaper;
use App\Models\User;
use App\Services\ReportService;
use App\Services\WorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
  $this->reportService = app(ReportService::class);
});

test('generateCsvExport creates a CSV file with concept paper data', function () {
  // Create test data
  $requisitioner = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $requisitioner->id,
    'status' => 'in_progress',
  ]);

  // Generate CSV
  $filePath = $this->reportService->generateCsvExport();

  // Assert file exists
  expect(file_exists($filePath))->toBeTrue();

  // Read CSV content
  $content = file_get_contents($filePath);
  expect($content)->toContain('Tracking Number');
  expect($content)->toContain($paper->tracking_number);

  // Clean up
  unlink($filePath);
});

test('getProcessingStatistics returns aggregate metrics', function () {
  // Create test data
  $requisitioner = User::factory()->create(['role' => 'requisitioner']);
  ConceptPaper::factory()->count(3)->create([
    'requisitioner_id' => $requisitioner->id,
    'status' => 'completed',
    'completed_at' => now(),
  ]);
  ConceptPaper::factory()->count(2)->create([
    'requisitioner_id' => $requisitioner->id,
    'status' => 'in_progress',
  ]);

  // Get statistics
  $stats = $this->reportService->getProcessingStatistics();

  // Assert structure
  expect($stats)->toHaveKeys([
    'total_papers',
    'by_status',
    'overdue_papers',
    'avg_processing_days',
    'median_processing_days',
    'completion_rate',
    'by_nature',
    'by_department',
    'monthly_trends',
  ]);

  // Assert values
  expect($stats['total_papers'])->toBe(5);
  expect($stats['by_status']['completed'])->toBe(3);
  expect($stats['by_status']['in_progress'])->toBe(2);
});

test('getStageAverages returns statistics for each workflow stage', function () {
  // Create test data
  $requisitioner = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create([
    'requisitioner_id' => $requisitioner->id,
  ]);

  // Initialize workflow
  $workflowService = app(WorkflowService::class);
  $workflowService->initializeWorkflow($paper);

  // Get stage averages
  $averages = $this->reportService->getStageAverages();

  // Assert structure
  expect($averages)->toBeArray();
  expect(count($averages))->toBe(10); // 10 workflow stages

  // Assert first stage data
  $firstStageData = collect($averages)->firstWhere('stage_order', 1);
  expect($firstStageData)->toHaveKeys([
    'stage_name',
    'stage_order',
    'assigned_role',
    'max_days',
    'avg_processing_hours',
    'total_count',
    'completed_count',
  ]);
});

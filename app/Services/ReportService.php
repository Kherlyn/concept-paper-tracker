<?php

namespace App\Services;

use App\Models\ConceptPaper;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportService
{
  /**
   * Generate CSV export of concept paper data with optional filters.
   *
   * @param array $filters
   * @return string Path to the generated CSV file
   */
  public function generateCsvExport(array $filters = []): string
  {
    $query = ConceptPaper::with(['requisitioner', 'currentStage', 'stages']);

    // Apply filters
    if (isset($filters['status'])) {
      $query->where('status', $filters['status']);
    }

    if (isset($filters['department'])) {
      $query->where('department', $filters['department']);
    }

    if (isset($filters['date_from'])) {
      $query->where('submitted_at', '>=', $filters['date_from']);
    }

    if (isset($filters['date_to'])) {
      $query->where('submitted_at', '<=', $filters['date_to']);
    }

    if (isset($filters['nature_of_request'])) {
      $query->where('nature_of_request', $filters['nature_of_request']);
    }

    $papers = $query->orderBy('submitted_at', 'desc')->get();

    // Generate CSV content
    $csvData = [];

    // CSV Headers
    $csvData[] = [
      'Tracking Number',
      'Requisitioner',
      'Department',
      'Title',
      'Nature of Request',
      'Submitted At',
      'Current Stage',
      'Status',
      'Completed At',
      'Processing Days',
      'Is Overdue',
    ];

    // CSV Rows
    foreach ($papers as $paper) {
      $processingDays = null;
      if ($paper->completed_at) {
        $processingDays = $paper->submitted_at->diffInDays($paper->completed_at);
      } elseif ($paper->submitted_at) {
        $processingDays = $paper->submitted_at->diffInDays(now());
      }

      $csvData[] = [
        $paper->tracking_number,
        $paper->requisitioner->name ?? 'N/A',
        $paper->department,
        $paper->title,
        ucfirst($paper->nature_of_request),
        $paper->submitted_at->format('Y-m-d H:i:s'),
        $paper->currentStage->stage_name ?? 'N/A',
        ucfirst($paper->status),
        $paper->completed_at ? $paper->completed_at->format('Y-m-d H:i:s') : 'N/A',
        $processingDays ?? 'N/A',
        $paper->isOverdue() ? 'Yes' : 'No',
      ];
    }

    // Create CSV file
    $fileName = 'concept_papers_export_' . now()->format('Y-m-d_His') . '.csv';
    $filePath = storage_path('app/reports/' . $fileName);

    // Ensure directory exists
    if (!file_exists(storage_path('app/reports'))) {
      mkdir(storage_path('app/reports'), 0755, true);
    }

    // Write CSV file
    $file = fopen($filePath, 'w');
    foreach ($csvData as $row) {
      fputcsv($file, $row);
    }
    fclose($file);

    return $filePath;
  }

  /**
   * Generate PDF report for an individual concept paper.
   *
   * @param ConceptPaper $paper
   * @return string Path to the generated PDF file
   */
  public function generatePdfReport(ConceptPaper $paper): string
  {
    // Load relationships
    $paper->load([
      'requisitioner',
      'stages.assignedUser',
      'attachments.uploader',
      'auditLogs.user',
    ]);

    // Calculate processing time
    $processingDays = null;
    if ($paper->completed_at) {
      $processingDays = $paper->submitted_at->diffInDays($paper->completed_at);
    } elseif ($paper->submitted_at) {
      $processingDays = $paper->submitted_at->diffInDays(now());
    }

    // Prepare data for PDF
    $data = [
      'paper' => $paper,
      'processingDays' => $processingDays,
      'generatedAt' => now()->format('F d, Y H:i:s'),
    ];

    // Generate PDF
    $pdf = Pdf::loadView('reports.concept-paper', $data);

    // Save PDF file
    $fileName = 'concept_paper_' . $paper->tracking_number . '_' . now()->format('Ymd_His') . '.pdf';
    $filePath = storage_path('app/reports/' . $fileName);

    // Ensure directory exists
    if (!file_exists(storage_path('app/reports'))) {
      mkdir(storage_path('app/reports'), 0755, true);
    }

    $pdf->save($filePath);

    return $filePath;
  }

  /**
   * Get aggregate processing statistics for all concept papers.
   *
   * @return array
   */
  public function getProcessingStatistics(): array
  {
    // Total papers by status
    $totalPapers = ConceptPaper::count();
    $pendingPapers = ConceptPaper::where('status', 'pending')->count();
    $inProgressPapers = ConceptPaper::where('status', 'in_progress')->count();
    $completedPapersCount = ConceptPaper::where('status', 'completed')->count();
    $returnedPapers = ConceptPaper::where('status', 'returned')->count();

    // Overdue papers
    $overduePapers = ConceptPaper::whereHas('stages', function ($query) {
      $query->whereIn('status', ['pending', 'in_progress'])
        ->where('deadline', '<', now());
    })->count();

    // Average processing time for completed papers
    $completedPapersCollection = ConceptPaper::where('status', 'completed')
      ->whereNotNull('completed_at')
      ->get(['submitted_at', 'completed_at']);

    $avgProcessingTime = null;
    if ($completedPapersCollection->count() > 0) {
      $totalDays = $completedPapersCollection->sum(function ($paper) {
        return $paper->submitted_at->diffInDays($paper->completed_at);
      });
      $avgProcessingTime = $totalDays / $completedPapersCollection->count();
    }

    // Median processing time for completed papers
    $completedPapersData = $completedPapersCollection->map(function ($paper) {
      return $paper->submitted_at->diffInDays($paper->completed_at);
    })->sort()->values();

    $medianProcessingTime = null;
    if ($completedPapersData->count() > 0) {
      $middle = floor($completedPapersData->count() / 2);
      if ($completedPapersData->count() % 2 == 0) {
        $medianProcessingTime = ($completedPapersData[$middle - 1] + $completedPapersData[$middle]) / 2;
      } else {
        $medianProcessingTime = $completedPapersData[$middle];
      }
    }

    // Papers by nature of request
    $papersByNature = ConceptPaper::select('nature_of_request', DB::raw('count(*) as count'))
      ->groupBy('nature_of_request')
      ->pluck('count', 'nature_of_request')
      ->toArray();

    // Papers by department
    $papersByDepartment = ConceptPaper::select('department', DB::raw('count(*) as count'))
      ->groupBy('department')
      ->orderByDesc('count')
      ->limit(10)
      ->pluck('count', 'department')
      ->toArray();

    // Monthly submission trends (last 12 months)
    $monthlyPapers = ConceptPaper::where('submitted_at', '>=', now()->subMonths(12))
      ->get(['submitted_at']);

    $monthlyTrends = $monthlyPapers->groupBy(function ($paper) {
      return $paper->submitted_at->format('Y-m');
    })->map(function ($group) {
      return $group->count();
    })->sortKeys()->toArray();

    // Completion rate
    $completionRate = $totalPapers > 0 ? round(($completedPapersCount / $totalPapers) * 100, 2) : 0;

    return [
      'total_papers' => $totalPapers,
      'by_status' => [
        'pending' => $pendingPapers,
        'in_progress' => $inProgressPapers,
        'completed' => $completedPapersCount,
        'returned' => $returnedPapers,
      ],
      'overdue_papers' => $overduePapers,
      'avg_processing_days' => $avgProcessingTime ? round($avgProcessingTime, 1) : 0,
      'median_processing_days' => $medianProcessingTime ? round($medianProcessingTime, 1) : 0,
      'completion_rate' => $completionRate,
      'by_nature' => $papersByNature,
      'by_department' => $papersByDepartment,
      'monthly_trends' => $monthlyTrends,
    ];
  }

  /**
   * Calculate average processing time for each workflow stage.
   *
   * @return array
   */
  public function getStageAverages(): array
  {
    $stages = config('workflow.stages');
    $stageAverages = [];

    foreach ($stages as $order => $stageConfig) {
      $stageName = $stageConfig['name'];

      // Get completed stages and calculate average time
      $completedStages = WorkflowStage::where('stage_name', $stageName)
        ->where('status', 'completed')
        ->whereNotNull('started_at')
        ->whereNotNull('completed_at')
        ->get(['started_at', 'completed_at']);

      $avgTime = null;
      if ($completedStages->count() > 0) {
        $totalHours = $completedStages->sum(function ($stage) {
          return $stage->started_at->diffInHours($stage->completed_at);
        });
        $avgTime = $totalHours / $completedStages->count();
      }

      // Get total count of stages
      $totalCount = WorkflowStage::where('stage_name', $stageName)->count();
      $completedCount = WorkflowStage::where('stage_name', $stageName)
        ->where('status', 'completed')
        ->count();
      $inProgressCount = WorkflowStage::where('stage_name', $stageName)
        ->where('status', 'in_progress')
        ->count();
      $overdueCount = WorkflowStage::where('stage_name', $stageName)
        ->whereIn('status', ['pending', 'in_progress'])
        ->where('deadline', '<', now())
        ->count();

      // Calculate completion rate for this stage
      $completionRate = $totalCount > 0 ? round(($completedCount / $totalCount) * 100, 2) : 0;

      // Convert hours to days and hours
      $avgDays = 0;
      $avgHours = 0;
      if ($avgTime) {
        $avgDays = floor($avgTime / 24);
        $avgHours = round($avgTime % 24, 1);
      }

      $stageAverages[] = [
        'stage_name' => $stageName,
        'stage_order' => $order,
        'assigned_role' => $stageConfig['role'],
        'max_days' => $stageConfig['max_days'],
        'avg_processing_hours' => $avgTime ? round($avgTime, 2) : 0,
        'avg_processing_days' => $avgDays,
        'avg_processing_hours_remainder' => $avgHours,
        'total_count' => $totalCount,
        'completed_count' => $completedCount,
        'in_progress_count' => $inProgressCount,
        'overdue_count' => $overdueCount,
        'completion_rate' => $completionRate,
      ];
    }

    return $stageAverages;
  }
}

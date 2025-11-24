<?php

namespace App\Services;

use App\Models\ConceptPaper;
use App\Models\Attachment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ConceptPaperService
{
  protected WorkflowService $workflowService;

  public function __construct(WorkflowService $workflowService)
  {
    $this->workflowService = $workflowService;
  }

  /**
   * Create a new concept paper and initialize its workflow.
   *
   * @param array $data
   * @param User $requisitioner
   * @return ConceptPaper
   */
  public function create(array $data, User $requisitioner): ConceptPaper
  {
    $conceptPaper = ConceptPaper::create([
      'requisitioner_id' => $requisitioner->id,
      'department' => $data['department'],
      'title' => $data['title'],
      'nature_of_request' => $data['nature_of_request'],
      'students_involved' => $data['students_involved'] ?? true,
      'deadline_option' => $data['deadline_option'] ?? null,
      'deadline_date' => $data['deadline_date'] ?? null,
      'submitted_at' => now(),
      'status' => 'pending',
    ]);

    // Initialize the workflow stages
    $this->workflowService->initializeWorkflow($conceptPaper);

    return $conceptPaper->fresh(['stages', 'currentStage']);
  }

  /**
   * Attach a file to a concept paper with validation.
   *
   * @param ConceptPaper $paper
   * @param UploadedFile $file
   * @param User $uploader
   * @return Attachment
   * @throws \Exception
   */
  public function attachFile(ConceptPaper $paper, UploadedFile $file, User $uploader): Attachment
  {
    return $this->attachFileToModel($paper, $file, $uploader, ConceptPaper::class);
  }

  /**
   * Attach a file to a workflow stage with validation.
   *
   * @param \App\Models\WorkflowStage $stage
   * @param UploadedFile $file
   * @param User $uploader
   * @return Attachment
   * @throws \Exception
   */
  public function attachFileToStage(\App\Models\WorkflowStage $stage, UploadedFile $file, User $uploader): Attachment
  {
    return $this->attachFileToModel($stage, $file, $uploader, \App\Models\WorkflowStage::class);
  }

  /**
   * Generic method to attach a file to any attachable model with validation.
   *
   * @param mixed $model
   * @param UploadedFile $file
   * @param User $uploader
   * @param string $modelClass
   * @return Attachment
   * @throws \Exception
   */
  protected function attachFileToModel($model, UploadedFile $file, User $uploader, string $modelClass): Attachment
  {
    // Get configuration values
    $maxSize = config('upload.max_file_size');
    $allowedMimeTypes = config('upload.allowed_mime_types');
    $storageDisk = config('upload.storage_disk');

    // Validate file type
    if (!in_array($file->getMimeType(), $allowedMimeTypes)) {
      throw new \Exception('Only PDF and Word documents (doc, docx) are allowed.');
    }

    // Validate file size
    if ($file->getSize() > $maxSize) {
      $maxSizeMB = round($maxSize / 1024 / 1024, 2);
      throw new \Exception("File size must not exceed {$maxSizeMB}MB.");
    }

    // Sanitize and generate unique file name
    $originalName = $file->getClientOriginalName();
    $extension = $file->getClientOriginalExtension();

    // Remove any potentially dangerous characters from filename
    $sanitizedName = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', pathinfo($originalName, PATHINFO_FILENAME));

    // Generate unique filename with timestamp and random string
    $uniqueFileName = $sanitizedName . '_' . time() . '_' . Str::random(8) . '.' . $extension;

    // Generate secure file path (organized by year/month)
    $year = date('Y');
    $month = date('m');
    $filePath = "{$year}/{$month}/{$uniqueFileName}";

    // Store the file
    $storedPath = $file->storeAs($filePath, $uniqueFileName, $storageDisk);

    if (!$storedPath) {
      throw new \Exception('Failed to store the file. Please try again.');
    }

    // Create attachment record
    $attachment = Attachment::create([
      'attachable_type' => $modelClass,
      'attachable_id' => $model->id,
      'file_name' => $originalName,
      'file_path' => $storedPath,
      'file_size' => $file->getSize(),
      'mime_type' => $file->getMimeType(),
      'uploaded_by' => $uploader->id,
    ]);

    return $attachment;
  }

  /**
   * Get status summary for a concept paper including progress data.
   *
   * @param ConceptPaper $paper
   * @return array
   */
  public function getStatusSummary(ConceptPaper $paper): array
  {
    $stages = $paper->stages()->get();

    $completedCount = $stages->where('status', 'completed')->count();
    $totalCount = $stages->count();
    $progressPercentage = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

    $currentStage = $paper->currentStage;
    $isOverdue = $paper->isOverdue();

    return [
      'tracking_number' => $paper->tracking_number,
      'status' => $paper->status,
      'current_stage' => $currentStage ? [
        'name' => $currentStage->stage_name,
        'assigned_role' => $currentStage->assigned_role,
        'deadline' => $currentStage->deadline,
        'is_overdue' => $currentStage->isOverdue(),
      ] : null,
      'progress' => [
        'completed' => $completedCount,
        'total' => $totalCount,
        'percentage' => $progressPercentage,
      ],
      'is_overdue' => $isOverdue,
      'submitted_at' => $paper->submitted_at,
      'completed_at' => $paper->completed_at,
    ];
  }

  /**
   * Get concept papers filtered by user role.
   *
   * @param User $user
   * @return Collection
   */
  public function getUserPapers(User $user): Collection
  {
    // Admin can see all papers
    if ($user->hasRole('admin')) {
      return ConceptPaper::with(['requisitioner', 'currentStage', 'stages'])
        ->orderBy('submitted_at', 'desc')
        ->get();
    }

    // Requisitioners see their own papers
    if ($user->hasRole('requisitioner')) {
      return ConceptPaper::where('requisitioner_id', $user->id)
        ->with(['requisitioner', 'currentStage', 'stages'])
        ->orderBy('submitted_at', 'desc')
        ->get();
    }

    // Approvers see papers assigned to their role
    return ConceptPaper::whereHas('stages', function ($query) use ($user) {
      $query->where('assigned_role', $user->role)
        ->whereIn('status', ['pending', 'in_progress']);
    })
      ->with(['requisitioner', 'currentStage', 'stages'])
      ->orderBy('submitted_at', 'desc')
      ->get();
  }

  /**
   * Get statistics for admin dashboard.
   *
   * @return array
   */
  public function getStatistics(): array
  {
    $totalPapers = ConceptPaper::count();
    $pendingPapers = ConceptPaper::where('status', 'pending')->count();
    $inProgressPapers = ConceptPaper::where('status', 'in_progress')->count();
    $completedPapers = ConceptPaper::where('status', 'completed')->count();
    $returnedPapers = ConceptPaper::where('status', 'returned')->count();

    // Get overdue papers
    $overduePapers = ConceptPaper::whereHas('stages', function ($query) {
      $query->whereIn('status', ['pending', 'in_progress'])
        ->where('deadline', '<', now());
    })->count();

    // Calculate average processing time for completed papers
    $completedPapersWithDates = ConceptPaper::where('status', 'completed')
      ->whereNotNull('completed_at')
      ->get(['submitted_at', 'completed_at']);

    $avgProcessingTime = $completedPapersWithDates->count() > 0
      ? $completedPapersWithDates->avg(function ($paper) {
        return $paper->submitted_at->diffInDays($paper->completed_at);
      })
      : 0;

    // Get stage statistics
    $stageStats = [];
    $stages = config('workflow.stages');

    foreach ($stages as $stageConfig) {
      $stageName = $stageConfig['name'];

      $stageStats[$stageName] = [
        'total' => \App\Models\WorkflowStage::where('stage_name', $stageName)->count(),
        'completed' => \App\Models\WorkflowStage::where('stage_name', $stageName)
          ->where('status', 'completed')
          ->count(),
        'in_progress' => \App\Models\WorkflowStage::where('stage_name', $stageName)
          ->where('status', 'in_progress')
          ->count(),
        'overdue' => \App\Models\WorkflowStage::where('stage_name', $stageName)
          ->whereIn('status', ['pending', 'in_progress'])
          ->where('deadline', '<', now())
          ->count(),
      ];
    }

    return [
      'total_papers' => $totalPapers,
      'by_status' => [
        'pending' => $pendingPapers,
        'in_progress' => $inProgressPapers,
        'completed' => $completedPapers,
        'returned' => $returnedPapers,
      ],
      'overdue_papers' => $overduePapers,
      'avg_processing_days' => round($avgProcessingTime, 1),
      'stage_statistics' => $stageStats,
    ];
  }
}

<?php

namespace App\Services;

use App\Models\ConceptPaper;
use App\Models\WorkflowStage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
  /**
   * Initialize workflow by creating all 10 stages for a concept paper.
   *
   * @param ConceptPaper $paper
   * @return void
   */
  public function initializeWorkflow(ConceptPaper $paper): void
  {
    $this->createStagesForPaper($paper);
  }

  /**
   * Create workflow stages for a concept paper with conditional routing.
   * Handles skipping of SPS stage when students are not involved.
   *
   * @param ConceptPaper $paper
   * @return void
   */
  public function createStagesForPaper(ConceptPaper $paper): void
  {
    $stages = config('workflow.stages');

    DB::transaction(function () use ($paper, $stages) {
      $actualOrder = 1;
      $firstStageId = null;

      foreach ($stages as $configOrder => $stageConfig) {
        // Check if this stage should be skipped
        if ($this->shouldSkipStage($paper, $stageConfig['name'])) {
          // Log the skipped stage in audit trail
          $paper->auditLogs()->create([
            'user_id' => $paper->requisitioner_id,
            'action' => 'stage_skipped',
            'stage_name' => $stageConfig['name'],
            'remarks' => 'Stage skipped due to no student involvement',
            'metadata' => [
              'reason' => 'students_involved_false',
              'config_order' => $configOrder,
            ],
          ]);
          continue;
        }

        $deadline = $this->calculateDeadline($stageConfig['name'], now());

        // Find a user with the assigned role
        $assignedUser = User::where('role', $stageConfig['role'])
          ->where('is_active', true)
          ->first();

        $stage = WorkflowStage::create([
          'concept_paper_id' => $paper->id,
          'stage_name' => $stageConfig['name'],
          'stage_order' => $actualOrder,
          'assigned_role' => $stageConfig['role'],
          'assigned_user_id' => $assignedUser?->id,
          'status' => $actualOrder === 1 ? 'in_progress' : 'pending',
          'started_at' => $actualOrder === 1 ? now() : null,
          'deadline' => $deadline,
        ]);

        // Track the first stage
        if ($actualOrder === 1) {
          $firstStageId = $stage->id;
        }

        $actualOrder++;
      }

      // Set the first stage as current stage
      if ($firstStageId) {
        $paper->current_stage_id = $firstStageId;
        $paper->status = 'in_progress';
        $paper->save();
      }
    });
  }

  /**
   * Determine if a stage should be skipped based on paper characteristics.
   *
   * @param ConceptPaper $paper
   * @param string $stageName
   * @return bool
   */
  public function shouldSkipStage(ConceptPaper $paper, string $stageName): bool
  {
    $stages = config('workflow.stages');

    // Find the stage configuration
    $stageConfig = null;
    foreach ($stages as $config) {
      if ($config['name'] === $stageName) {
        $stageConfig = $config;
        break;
      }
    }

    // If stage is not marked as skippable, don't skip
    if (!$stageConfig || !isset($stageConfig['skippable']) || !$stageConfig['skippable']) {
      return false;
    }

    // SPS Review stage should be skipped if students are not involved
    if ($stageName === 'SPS Review' && $paper->students_involved === false) {
      return true;
    }

    return false;
  }

  /**
   * Advance workflow to the next stage.
   *
   * @param WorkflowStage $stage
   * @param string|null $remarks
   * @param string|null $signature Base64 encoded signature image
   * @return void
   */
  public function advanceToNextStage(WorkflowStage $stage, ?string $remarks = null, ?string $signature = null): void
  {
    DB::transaction(function () use ($stage, $remarks, $signature) {
      // Complete the current stage with signature
      $stage->complete($remarks, $signature);

      $conceptPaper = $stage->conceptPaper;

      // Get the next stage
      $nextStageInfo = $this->getNextStage($stage);

      if ($nextStageInfo) {
        // Find the next stage record
        $nextStage = $conceptPaper->stages()
          ->where('stage_order', $stage->stage_order + 1)
          ->first();

        if ($nextStage) {
          // Start the next stage
          $nextStage->status = 'in_progress';
          $nextStage->started_at = now();
          $nextStage->save();

          // Update concept paper's current stage
          $conceptPaper->current_stage_id = $nextStage->id;
          $conceptPaper->save();
        }
      } else {
        // No more stages - mark concept paper as completed
        $conceptPaper->status = 'completed';
        $conceptPaper->completed_at = now();
        $conceptPaper->current_stage_id = null;
        $conceptPaper->save();
      }
    });
  }

  /**
   * Reject the workflow stage and concept paper.
   *
   * @param WorkflowStage $stage
   * @param string $rejectionReason
   * @return void
   */
  public function rejectStage(WorkflowStage $stage, string $rejectionReason): void
  {
    DB::transaction(function () use ($stage, $rejectionReason) {
      // Mark stage as rejected
      $stage->status = 'rejected';
      $stage->is_rejected = true;
      $stage->rejection_reason = $rejectionReason;
      $stage->rejected_at = now();
      $stage->completed_at = now();
      $stage->save();

      // Mark concept paper as rejected
      $conceptPaper = $stage->conceptPaper;
      $conceptPaper->status = 'rejected';
      $conceptPaper->current_stage_id = null;
      $conceptPaper->save();

      // Create audit log
      $conceptPaper->auditLogs()->create([
        'user_id' => auth()->id(),
        'action' => 'stage_rejected',
        'stage_name' => $stage->stage_name,
        'remarks' => $rejectionReason,
      ]);
    });
  }

  /**
   * Return workflow to the previous stage.
   *
   * @param WorkflowStage $stage
   * @param string $remarks
   * @return void
   */
  public function returnToPreviousStage(WorkflowStage $stage, string $remarks): void
  {
    DB::transaction(function () use ($stage, $remarks) {
      // Mark current stage as returned
      $stage->return($remarks);

      $conceptPaper = $stage->conceptPaper;

      // Find the previous stage
      $previousStage = $conceptPaper->stages()
        ->where('stage_order', $stage->stage_order - 1)
        ->first();

      if ($previousStage) {
        // Reopen the previous stage
        $previousStage->status = 'in_progress';
        $previousStage->started_at = now();
        $previousStage->completed_at = null;
        $previousStage->save();

        // Update concept paper's current stage
        $conceptPaper->current_stage_id = $previousStage->id;
        $conceptPaper->status = 'in_progress';
        $conceptPaper->save();
      }
    });
  }

  /**
   * Get the next stage for a workflow stage.
   * Returns the next stage information including proper routing through Senior VP Approval.
   *
   * @param WorkflowStage $currentStage
   * @return array|null
   */
  public function getNextStage(WorkflowStage $currentStage): ?array
  {
    $conceptPaper = $currentStage->conceptPaper;

    // Find the next stage record in the database
    $nextStage = $conceptPaper->stages()
      ->where('stage_order', $currentStage->stage_order + 1)
      ->first();

    if (!$nextStage) {
      return null;
    }

    // Return stage information
    return [
      'name' => $nextStage->stage_name,
      'order' => $nextStage->stage_order,
      'role' => $nextStage->assigned_role,
    ];
  }

  /**
   * Get the next stage definition from configuration.
   *
   * @param int $currentOrder
   * @return array|null
   */
  public function getNextStageDefinition(int $currentOrder): ?array
  {
    $stages = config('workflow.stages');
    $nextOrder = $currentOrder + 1;

    return $stages[$nextOrder] ?? null;
  }

  /**
   * Calculate deadline for a stage based on max_days configuration.
   *
   * @param string $stageName
   * @param Carbon $startTime
   * @return Carbon
   */
  public function calculateDeadline(string $stageName, Carbon $startTime): Carbon
  {
    $stages = config('workflow.stages');

    // Find the stage configuration by name
    foreach ($stages as $stageConfig) {
      if ($stageConfig['name'] === $stageName) {
        return $startTime->copy()->addDays($stageConfig['max_days']);
      }
    }

    // Default to 1 day if stage not found
    return $startTime->copy()->addDay();
  }

  /**
   * Check for overdue workflow stages across all concept papers.
   *
   * @return Collection
   */
  public function checkOverdueStages(): Collection
  {
    return WorkflowStage::whereIn('status', ['pending', 'in_progress'])
      ->where('deadline', '<', now())
      ->with(['conceptPaper', 'assignedUser'])
      ->get();
  }

  /**
   * Reassign a workflow stage to a different user.
   * Records the reassignment in the audit trail.
   *
   * @param WorkflowStage $stage
   * @param User $newUser
   * @param User $admin
   * @return void
   */
  public function reassignStage(WorkflowStage $stage, User $newUser, User $admin): void
  {
    DB::transaction(function () use ($stage, $newUser, $admin) {
      $oldUser = $stage->assignedUser;
      $oldUserId = $stage->assigned_user_id;

      // Update the stage assignment
      $stage->assigned_user_id = $newUser->id;
      $stage->save();

      // Create audit log entry
      $stage->conceptPaper->auditLogs()->create([
        'user_id' => $admin->id,
        'action' => 'stage_reassigned',
        'stage_name' => $stage->stage_name,
        'remarks' => sprintf(
          'Stage reassigned from %s to %s by administrator',
          $oldUser ? $oldUser->name : 'unassigned',
          $newUser->name
        ),
        'metadata' => [
          'old_user_id' => $oldUserId,
          'new_user_id' => $newUser->id,
          'admin_id' => $admin->id,
          'stage_order' => $stage->stage_order,
          'reassignment_reason' => 'admin_action',
        ],
      ]);
    });
  }
}

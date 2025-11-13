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
   * Initialize workflow by creating all 9 stages for a concept paper.
   *
   * @param ConceptPaper $paper
   * @return void
   */
  public function initializeWorkflow(ConceptPaper $paper): void
  {
    $stages = config('workflow.stages');

    DB::transaction(function () use ($paper, $stages) {
      foreach ($stages as $order => $stageConfig) {
        $deadline = $this->calculateDeadline($stageConfig['name'], now());

        // Find a user with the assigned role
        $assignedUser = User::where('role', $stageConfig['role'])
          ->where('is_active', true)
          ->first();

        $stage = WorkflowStage::create([
          'concept_paper_id' => $paper->id,
          'stage_name' => $stageConfig['name'],
          'stage_order' => $order,
          'assigned_role' => $stageConfig['role'],
          'assigned_user_id' => $assignedUser?->id,
          'status' => $order === 1 ? 'in_progress' : 'pending',
          'started_at' => $order === 1 ? now() : null,
          'deadline' => $deadline,
        ]);

        // Set the first stage as current stage
        if ($order === 1) {
          $paper->current_stage_id = $stage->id;
          $paper->status = 'in_progress';
          $paper->save();
        }
      }
    });
  }

  /**
   * Advance workflow to the next stage.
   *
   * @param WorkflowStage $stage
   * @param string|null $remarks
   * @return void
   */
  public function advanceToNextStage(WorkflowStage $stage, ?string $remarks = null): void
  {
    DB::transaction(function () use ($stage, $remarks) {
      // Complete the current stage
      $stage->complete($remarks);

      $conceptPaper = $stage->conceptPaper;

      // Get the next stage definition
      $nextStageDefinition = $this->getNextStageDefinition($stage->stage_order);

      if ($nextStageDefinition) {
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
}

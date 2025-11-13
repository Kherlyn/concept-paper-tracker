<?php

namespace App\Observers;

use App\Models\WorkflowStage;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class WorkflowStageObserver
{
  /**
   * Handle the WorkflowStage "updated" event.
   * Log stage completion with "completed" action and stage return with "returned" action.
   *
   * @param WorkflowStage $stage
   * @return void
   */
  public function updated(WorkflowStage $stage): void
  {
    // Get the changed attributes
    $changes = $stage->getChanges();

    // Remove timestamps from changes as they're not relevant for audit
    unset($changes['updated_at']);

    // Only log if there are meaningful changes
    if (empty($changes)) {
      return;
    }

    // Check if status changed to 'completed'
    if (isset($changes['status']) && $changes['status'] === 'completed') {
      AuditLog::create([
        'concept_paper_id' => $stage->concept_paper_id,
        'user_id' => Auth::id() ?? $stage->assigned_user_id,
        'action' => 'completed',
        'stage_name' => $stage->stage_name,
        'remarks' => $stage->remarks ?? 'Stage completed',
        'metadata' => [
          'stage_order' => $stage->stage_order,
          'completed_at' => $stage->completed_at?->toIso8601String(),
          'assigned_role' => $stage->assigned_role,
        ],
      ]);
      return;
    }

    // Check if status changed to 'returned'
    if (isset($changes['status']) && $changes['status'] === 'returned') {
      AuditLog::create([
        'concept_paper_id' => $stage->concept_paper_id,
        'user_id' => Auth::id() ?? $stage->assigned_user_id,
        'action' => 'returned',
        'stage_name' => $stage->stage_name,
        'remarks' => $stage->remarks ?? 'Stage returned to previous stage',
        'metadata' => [
          'stage_order' => $stage->stage_order,
          'assigned_role' => $stage->assigned_role,
        ],
      ]);
      return;
    }

    // Log other updates if they're significant
    if (isset($changes['remarks']) || isset($changes['assigned_user_id'])) {
      AuditLog::create([
        'concept_paper_id' => $stage->concept_paper_id,
        'user_id' => Auth::id() ?? $stage->assigned_user_id,
        'action' => 'stage_updated',
        'stage_name' => $stage->stage_name,
        'remarks' => 'Stage information updated',
        'metadata' => [
          'changes' => $changes,
          'stage_order' => $stage->stage_order,
        ],
      ]);
    }
  }
}

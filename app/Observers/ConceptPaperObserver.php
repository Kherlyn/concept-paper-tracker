<?php

namespace App\Observers;

use App\Models\ConceptPaper;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class ConceptPaperObserver
{
  /**
   * Handle the ConceptPaper "created" event.
   * Log creation event with "submitted" action.
   *
   * @param ConceptPaper $conceptPaper
   * @return void
   */
  public function created(ConceptPaper $conceptPaper): void
  {
    AuditLog::create([
      'concept_paper_id' => $conceptPaper->id,
      'user_id' => Auth::id() ?? $conceptPaper->requisitioner_id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Concept paper submitted',
      'metadata' => [
        'tracking_number' => $conceptPaper->tracking_number,
        'title' => $conceptPaper->title,
        'department' => $conceptPaper->department,
        'nature_of_request' => $conceptPaper->nature_of_request,
      ],
    ]);
  }

  /**
   * Handle the ConceptPaper "updated" event.
   * Log update events with changed fields.
   *
   * @param ConceptPaper $conceptPaper
   * @return void
   */
  public function updated(ConceptPaper $conceptPaper): void
  {
    // Get the changed attributes
    $changes = $conceptPaper->getChanges();

    // Remove timestamps from changes as they're not relevant for audit
    unset($changes['updated_at']);

    // Only log if there are meaningful changes
    if (empty($changes)) {
      return;
    }

    // Get original values for changed fields
    $original = [];
    foreach (array_keys($changes) as $key) {
      $original[$key] = $conceptPaper->getOriginal($key);
    }

    AuditLog::create([
      'concept_paper_id' => $conceptPaper->id,
      'user_id' => Auth::id() ?? $conceptPaper->requisitioner_id,
      'action' => 'updated',
      'stage_name' => null,
      'remarks' => 'Concept paper updated',
      'metadata' => [
        'changes' => $changes,
        'original' => $original,
      ],
    ]);
  }

  /**
   * Handle the ConceptPaper "deleted" event.
   * Log deletion events.
   *
   * @param ConceptPaper $conceptPaper
   * @return void
   */
  public function deleted(ConceptPaper $conceptPaper): void
  {
    AuditLog::create([
      'concept_paper_id' => $conceptPaper->id,
      'user_id' => Auth::id(),
      'action' => 'deleted',
      'stage_name' => null,
      'remarks' => 'Concept paper deleted',
      'metadata' => [
        'tracking_number' => $conceptPaper->tracking_number,
        'title' => $conceptPaper->title,
        'status' => $conceptPaper->status,
      ],
    ]);
  }
}

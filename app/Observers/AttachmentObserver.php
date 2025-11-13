<?php

namespace App\Observers;

use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\Auth;

class AttachmentObserver
{
  /**
   * Handle the Attachment "created" event.
   * Log attachment additions for workflow stages.
   *
   * @param Attachment $attachment
   * @return void
   */
  public function created(Attachment $attachment): void
  {
    // Only log attachments for WorkflowStage
    if ($attachment->attachable_type === WorkflowStage::class) {
      $stage = $attachment->attachable;

      if ($stage) {
        AuditLog::create([
          'concept_paper_id' => $stage->concept_paper_id,
          'user_id' => Auth::id() ?? $attachment->uploaded_by,
          'action' => 'attachment_added',
          'stage_name' => $stage->stage_name,
          'remarks' => "Attachment added: {$attachment->file_name}",
          'metadata' => [
            'file_name' => $attachment->file_name,
            'file_size' => $attachment->file_size,
            'mime_type' => $attachment->mime_type,
            'stage_order' => $stage->stage_order,
          ],
        ]);
      }
    }
  }
}

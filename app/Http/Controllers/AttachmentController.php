<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
  use AuthorizesRequests;

  /**
   * Download an attachment with authorization check.
   *
   * @param Request $request
   * @param Attachment $attachment
   * @return StreamedResponse
   */
  public function download(Request $request, Attachment $attachment): StreamedResponse
  {
    // Get the attachable (ConceptPaper or WorkflowStage)
    $attachable = $attachment->attachable;

    // Authorization check based on attachable type
    if ($attachable instanceof \App\Models\ConceptPaper) {
      // Check if user can view the concept paper
      $this->authorize('view', $attachable);
    } elseif ($attachable instanceof \App\Models\WorkflowStage) {
      // Check if user can view the workflow stage's concept paper
      $this->authorize('view', $attachable->conceptPaper);
    } else {
      abort(403, 'Unauthorized access to attachment.');
    }

    // Get storage disk
    $storageDisk = config('upload.storage_disk', 'concept_papers');

    // Check if file exists
    if (!Storage::disk($storageDisk)->exists($attachment->file_path)) {
      abort(404, 'File not found.');
    }

    // Stream the file download
    return Storage::disk($storageDisk)->download(
      $attachment->file_path,
      $attachment->file_name,
      [
        'Content-Type' => $attachment->mime_type,
      ]
    );
  }
}

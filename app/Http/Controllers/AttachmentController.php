<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\Contracts\DocumentPreviewServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttachmentController extends Controller
{
  use AuthorizesRequests;

  /**
   * Document preview service.
   *
   * @var DocumentPreviewServiceInterface
   */
  protected DocumentPreviewServiceInterface $previewService;

  /**
   * Create a new controller instance.
   *
   * @param DocumentPreviewServiceInterface $previewService
   */
  public function __construct(DocumentPreviewServiceInterface $previewService)
  {
    $this->previewService = $previewService;
  }

  /**
   * Preview an attachment in the browser.
   *
   * @param Attachment $attachment
   * @return Response|JsonResponse
   */
  public function preview(Attachment $attachment): Response|JsonResponse
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

    // Check if original file exists
    if (!Storage::disk($storageDisk)->exists($attachment->file_path)) {
      abort(404, 'File not found.');
    }

    try {
      // Convert document to preview format (PDF)
      $previewPath = $this->previewService->convertToPreviewFormat($attachment);

      if (!$previewPath) {
        // Conversion failed, return error response
        Log::warning("Preview conversion failed for attachment {$attachment->id}");
        return response()->json([
          'error' => 'Preview unavailable. Please download the file instead.',
          'download_url' => route('attachments.download', $attachment),
        ], 422);
      }

      // Check if converted file exists
      if (!Storage::disk($storageDisk)->exists($previewPath)) {
        Log::error("Converted preview file not found: {$previewPath}");
        return response()->json([
          'error' => 'Preview file not found. Please download the file instead.',
          'download_url' => route('attachments.download', $attachment),
        ], 404);
      }

      // Get the file content
      $fileContent = Storage::disk($storageDisk)->get($previewPath);

      // Generate preview filename (remove extension and add .pdf)
      $previewFilename = pathinfo($attachment->file_name, PATHINFO_FILENAME) . '_preview.pdf';

      // Return the PDF with appropriate headers for inline display
      return response($fileContent, 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="' . $previewFilename . '"')
        ->header('Cache-Control', 'max-age=86400, public') // Cache for 24 hours
        ->header('Expires', gmdate('D, d M Y H:i:s', time() + 86400) . ' GMT');
    } catch (\Exception $e) {
      Log::error("Error generating preview for attachment {$attachment->id}: {$e->getMessage()}", [
        'exception' => $e,
        'attachment_id' => $attachment->id,
        'file_name' => $attachment->file_name,
      ]);

      return response()->json([
        'error' => 'An error occurred while generating the preview. Please download the file instead.',
        'download_url' => route('attachments.download', $attachment),
      ], 500);
    }
  }

  /**
   * Download an attachment with authorization check.
   *
   * @param Attachment $attachment
   * @return StreamedResponse
   */
  public function download(Attachment $attachment): StreamedResponse
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

    // Get the file content
    $fileContent = Storage::disk($storageDisk)->get($attachment->file_path);

    // Return the file as a download
    return response()->streamDownload(
      function () use ($fileContent) {
        echo $fileContent;
      },
      $attachment->file_name,
      [
        'Content-Type' => $attachment->mime_type,
      ]
    );
  }
}

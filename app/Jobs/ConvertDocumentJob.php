<?php

namespace App\Jobs;

use App\Models\Attachment;
use App\Services\DocumentPreviewService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ConvertDocumentJob implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * The number of times the job may be attempted.
   *
   * @var int
   */
  public $tries = 3;

  /**
   * The number of seconds to wait before retrying the job.
   *
   * @var array<int, int>
   */
  public $backoff = [60, 300, 900]; // 1 min, 5 min, 15 min (exponential backoff)

  /**
   * The attachment to convert.
   *
   * @var Attachment
   */
  protected $attachment;

  /**
   * Create a new job instance.
   *
   * @param Attachment $attachment
   */
  public function __construct(Attachment $attachment)
  {
    $this->attachment = $attachment;
  }

  /**
   * Execute the job.
   *
   * @param DocumentPreviewService $documentPreviewService
   * @return void
   */
  public function handle(DocumentPreviewService $documentPreviewService): void
  {
    Log::info('ConvertDocumentJob started', [
      'attachment_id' => $this->attachment->id,
      'file_name' => $this->attachment->file_name,
    ]);

    try {
      $previewPath = $documentPreviewService->convertToPreviewFormat($this->attachment);

      if ($previewPath) {
        Log::info('Document conversion successful', [
          'attachment_id' => $this->attachment->id,
          'preview_path' => $previewPath,
        ]);
      } else {
        Log::warning('Document conversion returned null', [
          'attachment_id' => $this->attachment->id,
        ]);
      }
    } catch (\Exception $e) {
      Log::error('ConvertDocumentJob failed', [
        'attachment_id' => $this->attachment->id,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
      ]);

      // Re-throw to trigger retry logic
      throw $e;
    }
  }

  /**
   * Handle a job failure.
   *
   * @param \Throwable $exception
   * @return void
   */
  public function failed(\Throwable $exception): void
  {
    Log::error('ConvertDocumentJob failed permanently after all retries', [
      'attachment_id' => $this->attachment->id,
      'file_name' => $this->attachment->file_name,
      'error' => $exception->getMessage(),
    ]);

    // Could notify admins here or mark the attachment as conversion-failed
  }
}

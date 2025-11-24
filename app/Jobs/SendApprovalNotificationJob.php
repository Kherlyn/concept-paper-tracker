<?php

namespace App\Jobs;

use App\Models\ConceptPaper;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendApprovalNotificationJob implements ShouldQueue
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
   * The concept paper that was approved.
   *
   * @var ConceptPaper
   */
  protected $conceptPaper;

  /**
   * Create a new job instance.
   *
   * @param ConceptPaper $conceptPaper
   */
  public function __construct(ConceptPaper $conceptPaper)
  {
    $this->conceptPaper = $conceptPaper;
  }

  /**
   * Execute the job.
   *
   * @param NotificationService $notificationService
   * @return void
   */
  public function handle(NotificationService $notificationService): void
  {
    Log::info('SendApprovalNotificationJob started', [
      'concept_paper_id' => $this->conceptPaper->id,
      'tracking_number' => $this->conceptPaper->tracking_number,
    ]);

    try {
      $notificationService->sendApprovalNotification($this->conceptPaper);

      Log::info('Approval notification sent successfully', [
        'concept_paper_id' => $this->conceptPaper->id,
      ]);
    } catch (\Exception $e) {
      Log::error('SendApprovalNotificationJob failed', [
        'concept_paper_id' => $this->conceptPaper->id,
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
    Log::error('SendApprovalNotificationJob failed permanently after all retries', [
      'concept_paper_id' => $this->conceptPaper->id,
      'tracking_number' => $this->conceptPaper->tracking_number,
      'error' => $exception->getMessage(),
    ]);

    // Could notify admins about the failure
  }
}

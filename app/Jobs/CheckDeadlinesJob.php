<?php

namespace App\Jobs;

use App\Models\ConceptPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class CheckDeadlinesJob implements ShouldQueue
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
   * Create a new job instance.
   */
  public function __construct()
  {
    //
  }

  /**
   * Execute the job.
   *
   * @return void
   */
  public function handle(): void
  {
    Log::info('CheckDeadlinesJob started');

    try {
      // Query concept papers that have reached their deadline and are not completed
      $papersWithDeadlines = ConceptPaper::whereNotNull('deadline_date')
        ->where('deadline_date', '<=', now())
        ->where('status', '!=', 'completed')
        ->with(['requisitioner', 'currentStage.assignedUser'])
        ->get();

      Log::info('Found ' . $papersWithDeadlines->count() . ' papers with reached deadlines');

      foreach ($papersWithDeadlines as $paper) {
        // Use cache to ensure we only send one notification per paper
        $cacheKey = "deadline_notification_sent_{$paper->id}";

        if (!Cache::has($cacheKey)) {
          // Dispatch the notification job
          SendDeadlineNotificationJob::dispatch($paper);

          // Mark as notified (cache for 7 days to prevent duplicate notifications)
          Cache::put($cacheKey, true, now()->addDays(7));

          Log::info('Dispatched deadline notification', [
            'concept_paper_id' => $paper->id,
            'tracking_number' => $paper->tracking_number,
          ]);
        } else {
          Log::debug('Deadline notification already sent', [
            'concept_paper_id' => $paper->id,
          ]);
        }
      }

      Log::info('CheckDeadlinesJob completed successfully');
    } catch (\Exception $e) {
      Log::error('CheckDeadlinesJob failed', [
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
    Log::error('CheckDeadlinesJob failed permanently after all retries', [
      'error' => $exception->getMessage(),
    ]);

    // Could notify admins about the system-wide failure
  }
}

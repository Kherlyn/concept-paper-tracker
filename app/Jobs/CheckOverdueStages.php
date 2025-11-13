<?php

namespace App\Jobs;

use App\Models\WorkflowStage;
use App\Services\NotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CheckOverdueStages implements ShouldQueue
{
  use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

  /**
   * Create a new job instance.
   */
  public function __construct()
  {
    //
  }

  /**
   * Execute the job.
   */
  public function handle(NotificationService $notificationService): void
  {
    Log::info('CheckOverdueStages job started');

    // Query workflow stages that are past deadline and not completed
    $overdueStages = WorkflowStage::whereIn('status', ['pending', 'in_progress'])
      ->where('deadline', '<', now())
      ->with(['conceptPaper', 'assignedUser'])
      ->get();

    Log::info('Found ' . $overdueStages->count() . ' overdue stages');

    // Send notifications for each overdue stage
    foreach ($overdueStages as $stage) {
      try {
        $notificationService->notifyOverdue($stage);
        Log::info('Sent overdue notification for stage ' . $stage->id);
      } catch (\Exception $e) {
        Log::error('Failed to send overdue notification for stage ' . $stage->id . ': ' . $e->getMessage());
      }
    }

    Log::info('CheckOverdueStages job completed');
  }
}

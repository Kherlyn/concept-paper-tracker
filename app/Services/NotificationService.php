<?php

namespace App\Services;

use App\Models\ConceptPaper;
use App\Models\User;
use App\Models\WorkflowStage;
use App\Notifications\ApprovalNotification;
use App\Notifications\DeadlineReachedNotification;
use App\Notifications\PaperCompletedNotification;
use App\Notifications\PaperReturnedNotification;
use App\Notifications\StageAssignedNotification;
use App\Notifications\StageOverdueNotification;
use App\Notifications\StageReassignmentNotification;

class NotificationService
{
  /**
   * Send notification when a stage is assigned to a user.
   *
   * @param WorkflowStage $stage
   * @return void
   */
  public function notifyStageAssignment(WorkflowStage $stage): void
  {
    // Find users with the assigned role
    $users = User::where('role', $stage->assigned_role)
      ->where('is_active', true)
      ->get();

    // If a specific user is assigned, notify only that user
    if ($stage->assigned_user_id) {
      $user = User::find($stage->assigned_user_id);
      if ($user && $user->is_active) {
        $user->notify(new StageAssignedNotification($stage));
      }
    } else {
      // Otherwise, notify all users with the assigned role
      foreach ($users as $user) {
        $user->notify(new StageAssignedNotification($stage));
      }
    }
  }

  /**
   * Send notification when a stage is overdue.
   *
   * @param WorkflowStage $stage
   * @return void
   */
  public function notifyOverdue(WorkflowStage $stage): void
  {
    // Find users with the assigned role
    $users = User::where('role', $stage->assigned_role)
      ->where('is_active', true)
      ->get();

    // If a specific user is assigned, notify only that user
    if ($stage->assigned_user_id) {
      $user = User::find($stage->assigned_user_id);
      if ($user && $user->is_active) {
        $user->notify(new StageOverdueNotification($stage));
      }
    } else {
      // Otherwise, notify all users with the assigned role
      foreach ($users as $user) {
        $user->notify(new StageOverdueNotification($stage));
      }
    }
  }

  /**
   * Send notification when a concept paper workflow is completed.
   *
   * @param ConceptPaper $conceptPaper
   * @return void
   */
  public function notifyCompletion(ConceptPaper $conceptPaper): void
  {
    // Notify the requisitioner
    $requisitioner = $conceptPaper->requisitioner;
    if ($requisitioner && $requisitioner->is_active) {
      $requisitioner->notify(new PaperCompletedNotification($conceptPaper));
    }

    // Optionally notify admin users
    $admins = User::where('role', 'admin')
      ->where('is_active', true)
      ->get();

    foreach ($admins as $admin) {
      $admin->notify(new PaperCompletedNotification($conceptPaper));
    }
  }

  /**
   * Send notification when a concept paper is returned to previous stage.
   *
   * @param WorkflowStage $stage
   * @return void
   */
  public function notifyReturn(WorkflowStage $stage): void
  {
    $conceptPaper = $stage->conceptPaper;

    // Notify the requisitioner
    $requisitioner = $conceptPaper->requisitioner;
    if ($requisitioner && $requisitioner->is_active) {
      $requisitioner->notify(new PaperReturnedNotification($stage, $stage->remarks ?? ''));
    }

    // Find the previous stage to notify the user who needs to handle it
    $previousStage = $conceptPaper->stages()
      ->where('stage_order', '<', $stage->stage_order)
      ->orderBy('stage_order', 'desc')
      ->first();

    if ($previousStage) {
      // Notify users with the role of the previous stage
      $users = User::where('role', $previousStage->assigned_role)
        ->where('is_active', true)
        ->get();

      foreach ($users as $user) {
        $user->notify(new PaperReturnedNotification($stage, $stage->remarks ?? ''));
      }
    }
  }

  /**
   * Send notification when a concept paper reaches its deadline.
   *
   * @param ConceptPaper $conceptPaper
   * @return void
   */
  public function sendDeadlineReachedNotification(ConceptPaper $conceptPaper): void
  {
    // Notify the requisitioner
    $requisitioner = $conceptPaper->requisitioner;
    if ($requisitioner && $requisitioner->is_active) {
      $requisitioner->notify(new DeadlineReachedNotification($conceptPaper));
    }

    // Notify the current stage assignee
    if ($conceptPaper->currentStage && $conceptPaper->currentStage->assigned_user_id) {
      $assignedUser = User::find($conceptPaper->currentStage->assigned_user_id);
      if ($assignedUser && $assignedUser->is_active) {
        $assignedUser->notify(new DeadlineReachedNotification($conceptPaper));
      }
    } elseif ($conceptPaper->currentStage) {
      // If no specific user assigned, notify all users with the assigned role
      $users = User::where('role', $conceptPaper->currentStage->assigned_role)
        ->where('is_active', true)
        ->get();

      foreach ($users as $user) {
        $user->notify(new DeadlineReachedNotification($conceptPaper));
      }
    }
  }

  /**
   * Send notification when a concept paper is approved and completed.
   *
   * @param ConceptPaper $conceptPaper
   * @return void
   */
  public function sendApprovalNotification(ConceptPaper $conceptPaper): void
  {
    // Notify the requisitioner
    $requisitioner = $conceptPaper->requisitioner;
    if ($requisitioner && $requisitioner->is_active) {
      $requisitioner->notify(new ApprovalNotification($conceptPaper));
    }

    // Notify administrators
    $admins = User::where('role', 'admin')
      ->where('is_active', true)
      ->get();

    foreach ($admins as $admin) {
      $admin->notify(new ApprovalNotification($conceptPaper));
    }
  }

  /**
   * Send notification when a workflow stage is reassigned to a new user.
   *
   * @param WorkflowStage $stage
   * @param User $newUser
   * @param User $previousUser
   * @return void
   */
  public function sendStageReassignmentNotification(WorkflowStage $stage, User $newUser, User $previousUser): void
  {
    // Notify the newly assigned user
    if ($newUser->is_active) {
      $newUser->notify(new StageReassignmentNotification($stage, $previousUser));
    }
  }
}

<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WorkflowStage;

class WorkflowStagePolicy
{
  /**
   * Determine if the user can complete the workflow stage.
   * Only the assigned user with matching role can complete.
   *
   * @param User $user
   * @param WorkflowStage $stage
   * @return bool
   */
  public function complete(User $user, WorkflowStage $stage): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Stage must not already be completed
    if ($stage->status === 'completed') {
      return false;
    }

    // User must have the role assigned to this stage
    if ($user->role !== $stage->assigned_role) {
      return false;
    }

    // If a specific user is assigned, it must be this user
    if ($stage->assigned_user_id !== null && $stage->assigned_user_id !== $user->id) {
      return false;
    }

    return true;
  }

  /**
   * Determine if the user can return the workflow stage to the previous stage.
   * Only the assigned user can return to previous stage.
   *
   * @param User $user
   * @param WorkflowStage $stage
   * @return bool
   */
  public function return(User $user, WorkflowStage $stage): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Stage must not already be completed
    if ($stage->status === 'completed') {
      return false;
    }

    // User must have the role assigned to this stage
    if ($user->role !== $stage->assigned_role) {
      return false;
    }

    // If a specific user is assigned, it must be this user
    if ($stage->assigned_user_id !== null && $stage->assigned_user_id !== $user->id) {
      return false;
    }

    // Cannot return the first stage (stage_order 1)
    if ($stage->stage_order === 1) {
      return false;
    }

    return true;
  }

  /**
   * Determine if the user can add attachments to the workflow stage.
   * Only the assigned user can add attachments.
   *
   * @param User $user
   * @param WorkflowStage $stage
   * @return bool
   */
  public function addAttachment(User $user, WorkflowStage $stage): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Stage must not be completed
    if ($stage->status === 'completed') {
      return false;
    }

    // User must have the role assigned to this stage
    if ($user->role !== $stage->assigned_role) {
      return false;
    }

    // If a specific user is assigned, it must be this user
    if ($stage->assigned_user_id !== null && $stage->assigned_user_id !== $user->id) {
      return false;
    }

    return true;
  }
}

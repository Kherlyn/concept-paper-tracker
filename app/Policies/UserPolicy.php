<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
  /**
   * Determine if the user can manage user activation.
   * Only admins can activate/deactivate users.
   *
   * @param User $user
   * @return bool
   */
  public function manageActivation(User $user): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Only admins can manage user activation
    return $user->role === 'admin';
  }

  /**
   * Determine if the user can toggle activation status of a target user.
   * Only admins can toggle activation, and they cannot deactivate themselves.
   *
   * @param User $user
   * @param User $targetUser
   * @return bool
   */
  public function toggleActivation(User $user, User $targetUser): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Only admins can toggle activation
    if ($user->role !== 'admin') {
      return false;
    }

    // Cannot deactivate yourself
    if ($user->id === $targetUser->id) {
      return false;
    }

    return true;
  }

  /**
   * Determine if the user can view assigned stages for another user.
   * Only admins can view assigned stages.
   *
   * @param User $user
   * @return bool
   */
  public function viewAssignedStages(User $user): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Only admins can view assigned stages
    return $user->role === 'admin';
  }

  /**
   * Determine if the user can manage other users.
   * Only admins can create, update, or delete users.
   *
   * @param User $user
   * @return bool
   */
  public function manage(User $user): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Only admins can manage users
    return $user->role === 'admin';
  }
}

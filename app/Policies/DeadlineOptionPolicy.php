<?php

namespace App\Policies;

use App\Models\DeadlineOption;
use App\Models\User;

class DeadlineOptionPolicy
{
  /**
   * Determine if the user can view deadline options.
   * All authenticated users can view deadline options.
   *
   * @param User $user
   * @return bool
   */
  public function viewAny(User $user): bool
  {
    // All active users can view deadline options
    return $user->is_active;
  }

  /**
   * Determine if the user can create deadline options.
   * Only admins can create deadline options.
   *
   * @param User $user
   * @return bool
   */
  public function create(User $user): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Only admins can create deadline options
    return $user->role === 'admin';
  }

  /**
   * Determine if the user can update deadline options.
   * Only admins can update deadline options.
   *
   * @param User $user
   * @param DeadlineOption $deadlineOption
   * @return bool
   */
  public function update(User $user, DeadlineOption $deadlineOption): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Only admins can update deadline options
    return $user->role === 'admin';
  }

  /**
   * Determine if the user can delete deadline options.
   * Only admins can delete deadline options.
   *
   * @param User $user
   * @param DeadlineOption $deadlineOption
   * @return bool
   */
  public function delete(User $user, DeadlineOption $deadlineOption): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Only admins can delete deadline options
    return $user->role === 'admin';
  }

  /**
   * Determine if the user can manage deadline options.
   * Only admins can manage deadline options.
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

    // Only admins can manage deadline options
    return $user->role === 'admin';
  }
}

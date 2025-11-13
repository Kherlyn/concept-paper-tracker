<?php

namespace App\Policies;

use App\Models\ConceptPaper;
use App\Models\User;

class ConceptPaperPolicy
{
  /**
   * Determine if the user can view the concept paper.
   * Requisitioners can view their own papers, approvers can view assigned papers.
   *
   * @param User $user
   * @param ConceptPaper $conceptPaper
   * @return bool
   */
  public function view(User $user, ConceptPaper $conceptPaper): bool
  {
    // Admin can view all papers
    if ($user->hasRole('admin')) {
      return true;
    }

    // Requisitioner can view their own papers
    if ($user->id === $conceptPaper->requisitioner_id) {
      return true;
    }

    // Approvers can view papers that have stages assigned to their role
    $hasAssignedStage = $conceptPaper->stages()
      ->where('assigned_role', $user->role)
      ->exists();

    return $hasAssignedStage;
  }

  /**
   * Determine if the user can create concept papers.
   * Only requisitioners can create concept papers.
   *
   * @param User $user
   * @return bool
   */
  public function create(User $user): bool
  {
    return $user->hasRole('requisitioner') && $user->is_active;
  }

  /**
   * Determine if the user can update the concept paper.
   * Only the requisitioner can update, and only before the first approval.
   *
   * @param User $user
   * @param ConceptPaper $conceptPaper
   * @return bool
   */
  public function update(User $user, ConceptPaper $conceptPaper): bool
  {
    // Only the requisitioner can update their own paper
    if ($user->id !== $conceptPaper->requisitioner_id) {
      return false;
    }

    // Cannot update if user is not active
    if (!$user->is_active) {
      return false;
    }

    // Cannot update if paper is completed
    if ($conceptPaper->status === 'completed') {
      return false;
    }

    // Check if any stage has been completed (first approval has happened)
    $hasCompletedStage = $conceptPaper->stages()
      ->where('status', 'completed')
      ->exists();

    // Can only update before first approval
    return !$hasCompletedStage;
  }

  /**
   * Determine if the user can delete the concept paper.
   * Only admin can soft delete concept papers.
   *
   * @param User $user
   * @param ConceptPaper $conceptPaper
   * @return bool
   */
  public function delete(User $user, ConceptPaper $conceptPaper): bool
  {
    return $user->hasRole('admin') && $user->is_active;
  }
}

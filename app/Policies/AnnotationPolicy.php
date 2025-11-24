<?php

namespace App\Policies;

use App\Models\Annotation;
use App\Models\ConceptPaper;
use App\Models\User;

class AnnotationPolicy
{
  /**
   * Determine if the user can create annotations on a concept paper.
   * Users can annotate papers they have access to view.
   *
   * @param User $user
   * @param ConceptPaper $conceptPaper
   * @return bool
   */
  public function create(User $user, ConceptPaper $conceptPaper): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Check if user can view the concept paper (using ConceptPaperPolicy logic)
    // Admin can annotate all papers
    if ($user->role === 'admin') {
      return true;
    }

    // Requisitioner can annotate their own papers
    if ($user->id === $conceptPaper->requisitioner_id) {
      return true;
    }

    // Approvers can annotate papers that have stages assigned to their role
    $hasAssignedStage = $conceptPaper->stages()
      ->where('assigned_role', $user->role)
      ->exists();

    return $hasAssignedStage;
  }

  /**
   * Determine if the user can update an annotation.
   * Only the creator or admin can update annotations.
   *
   * @param User $user
   * @param Annotation $annotation
   * @return bool
   */
  public function update(User $user, Annotation $annotation): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Admin can update any annotation
    if ($user->role === 'admin') {
      return true;
    }

    // User can update their own annotations
    return $user->id === $annotation->user_id;
  }

  /**
   * Determine if the user can delete an annotation.
   * Only the creator or admin can delete annotations.
   *
   * @param User $user
   * @param Annotation $annotation
   * @return bool
   */
  public function delete(User $user, Annotation $annotation): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Admin can delete any annotation
    if ($user->role === 'admin') {
      return true;
    }

    // User can delete their own annotations
    return $user->id === $annotation->user_id;
  }

  /**
   * Determine if the user can view annotations on a concept paper.
   * Users can view annotations on papers they have access to.
   *
   * @param User $user
   * @param ConceptPaper $conceptPaper
   * @return bool
   */
  public function view(User $user, ConceptPaper $conceptPaper): bool
  {
    // User must be active
    if (!$user->is_active) {
      return false;
    }

    // Check if user can view the concept paper (using ConceptPaperPolicy logic)
    // Admin can view all annotations
    if ($user->role === 'admin') {
      return true;
    }

    // Requisitioner can view annotations on their own papers
    if ($user->id === $conceptPaper->requisitioner_id) {
      return true;
    }

    // Approvers can view annotations on papers that have stages assigned to their role
    $hasAssignedStage = $conceptPaper->stages()
      ->where('assigned_role', $user->role)
      ->exists();

    return $hasAssignedStage;
  }
}

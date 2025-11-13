<?php

namespace App\Http\Middleware;

use App\Models\WorkflowStage;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StageAccessMiddleware
{
  /**
   * Handle an incoming request.
   * Verify user has permission to access specific workflow stage
   * and check if stage is in correct status for the action.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  public function handle(Request $request, Closure $next): Response
  {
    // Ensure user is authenticated
    if (!$request->user()) {
      abort(401, 'Unauthenticated.');
    }

    // Get the workflow stage from the route parameter
    $stageId = $request->route('stage') ?? $request->route('id');

    if (!$stageId) {
      abort(400, 'Workflow stage not specified.');
    }

    // Find the workflow stage
    $stage = WorkflowStage::find($stageId);

    if (!$stage) {
      abort(404, 'Workflow stage not found.');
    }

    $user = $request->user();

    // Check if user is active
    if (!$user->is_active) {
      abort(403, 'Your account is inactive. Please contact an administrator.');
    }

    // Admin can access all stages
    if ($user->hasRole('admin')) {
      return $next($request);
    }

    // Check if user has the role assigned to this stage
    if ($user->role !== $stage->assigned_role) {
      abort(403, 'You do not have permission to access this workflow stage.');
    }

    // If a specific user is assigned, verify it's this user
    if ($stage->assigned_user_id !== null && $stage->assigned_user_id !== $user->id) {
      abort(403, 'This workflow stage is assigned to a different user.');
    }

    // Check if stage is in a valid status for modification
    $method = $request->method();

    // For POST/PUT/PATCH requests (actions), stage must be pending or in_progress
    if (in_array($method, ['POST', 'PUT', 'PATCH'])) {
      if (!in_array($stage->status, ['pending', 'in_progress'])) {
        abort(422, 'This workflow stage cannot be modified in its current status: ' . $stage->status);
      }
    }

    return $next($request);
  }
}

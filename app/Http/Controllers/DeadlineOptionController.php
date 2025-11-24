<?php

namespace App\Http\Controllers;

use App\Models\DeadlineOption;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class DeadlineOptionController extends Controller
{
  use AuthorizesRequests;
  /**
   * Get all available deadline options.
   *
   * @return JsonResponse
   */
  public function index(): JsonResponse
  {
    $deadlineOptions = DeadlineOption::orderBy('sort_order')->get();

    return response()->json([
      'success' => true,
      'deadline_options' => $deadlineOptions->map(function ($option) {
        return [
          'id' => $option->id,
          'key' => $option->key,
          'label' => $option->label,
          'days' => $option->days,
          'sort_order' => $option->sort_order,
        ];
      }),
    ]);
  }

  /**
   * Store a new deadline option.
   * Admin only - adds a new deadline option to the database.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request): JsonResponse
  {
    // Authorization check using policy
    $user = Auth::user();
    if (!$user) {
      return response()->json([
        'error' => 'Unauthenticated.'
      ], 401);
    }
    $this->authorize('create', \App\Models\DeadlineOption::class);

    // Validate the request
    $validated = $request->validate([
      'key' => ['required', 'string', 'regex:/^[a-z0-9_]+$/', 'max:50', 'unique:deadline_options,key'],
      'label' => ['required', 'string', 'max:100'],
      'days' => ['required', 'integer', 'min:1', 'max:365'],
      'sort_order' => ['nullable', 'integer', 'min:0'],
    ]);

    // Set default sort_order if not provided
    if (!isset($validated['sort_order'])) {
      $maxSortOrder = DeadlineOption::max('sort_order') ?? 0;
      $validated['sort_order'] = $maxSortOrder + 1;
    }

    // Create new option
    $deadlineOption = DeadlineOption::create($validated);

    return response()->json([
      'success' => true,
      'message' => 'Deadline option created successfully.',
      'deadline_option' => [
        'id' => $deadlineOption->id,
        'key' => $deadlineOption->key,
        'label' => $deadlineOption->label,
        'days' => $deadlineOption->days,
        'sort_order' => $deadlineOption->sort_order,
      ],
    ], 201);
  }

  /**
   * Update an existing deadline option.
   * Admin only - updates a deadline option in the database.
   *
   * @param Request $request
   * @param string $key
   * @return JsonResponse
   */
  public function update(Request $request, string $key): JsonResponse
  {
    // Find the deadline option by key
    $deadlineOption = DeadlineOption::where('key', $key)->first();

    if (!$deadlineOption) {
      return response()->json([
        'error' => 'Deadline option not found.'
      ], 404);
    }

    // Authorization check using policy
    $this->authorize('update', $deadlineOption);

    // Validate the request
    $validated = $request->validate([
      'label' => ['required', 'string', 'max:100'],
      'days' => ['required', 'integer', 'min:1', 'max:365'],
      'sort_order' => ['nullable', 'integer', 'min:0'],
    ]);

    // Update the option
    $deadlineOption->update($validated);

    return response()->json([
      'success' => true,
      'message' => 'Deadline option updated successfully.',
      'deadline_option' => [
        'id' => $deadlineOption->id,
        'key' => $deadlineOption->key,
        'label' => $deadlineOption->label,
        'days' => $deadlineOption->days,
        'sort_order' => $deadlineOption->sort_order,
      ],
    ]);
  }

  /**
   * Delete a deadline option.
   * Admin only - removes a deadline option from the database.
   *
   * @param string $key
   * @return JsonResponse
   */
  public function destroy(string $key): JsonResponse
  {
    // Find the deadline option by key
    $deadlineOption = DeadlineOption::where('key', $key)->first();

    if (!$deadlineOption) {
      return response()->json([
        'error' => 'Deadline option not found.'
      ], 404);
    }

    // Authorization check using policy
    $this->authorize('delete', $deadlineOption);

    // Delete the option
    $deadlineOption->delete();

    return response()->json([
      'success' => true,
      'message' => 'Deadline option deleted successfully.',
    ]);
  }
}

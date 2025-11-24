<?php

namespace App\Http\Controllers;

use App\Models\Annotation;
use App\Models\ConceptPaper;
use App\Services\Contracts\AnnotationServiceInterface;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AnnotationController extends Controller
{
  use AuthorizesRequests;

  protected AnnotationServiceInterface $annotationService;

  public function __construct(AnnotationServiceInterface $annotationService)
  {
    $this->annotationService = $annotationService;
  }

  /**
   * Fetch annotations for a specific document/page.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request): JsonResponse
  {
    $validated = $request->validate([
      'attachment_id' => 'required|integer|exists:attachments,id',
      'page_number' => 'nullable|integer|min:1',
    ]);

    try {
      $annotations = $this->annotationService->getAnnotationsForDocument(
        $validated['attachment_id'],
        $validated['page_number'] ?? null
      );

      return response()->json([
        'success' => true,
        'data' => $annotations,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to fetch annotations: ' . $e->getMessage(),
      ], 500);
    }
  }

  /**
   * Create a new annotation.
   *
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request): JsonResponse
  {
    // Get the authenticated user
    $user = Auth::user();

    if (!$user) {
      return response()->json([
        'success' => false,
        'message' => 'Unauthenticated.',
      ], 401);
    }

    // Validate basic required fields
    $validated = $request->validate([
      'concept_paper_id' => 'required|integer|exists:concept_papers,id',
      'attachment_id' => 'required|integer|exists:attachments,id',
      'page_number' => 'required|integer|min:1',
      'annotation_type' => 'required|in:marker,highlight,discrepancy,drawing',
      'coordinates' => 'required|array',
      'comment' => 'nullable|string|max:5000',
      'is_discrepancy' => 'boolean',
      'max_width' => 'nullable|integer|min:1',
      'max_height' => 'nullable|integer|min:1',
    ]);

    // Check if user can annotate the concept paper
    $conceptPaper = ConceptPaper::findOrFail($validated['concept_paper_id']);
    $this->authorize('annotate', $conceptPaper);

    // Add user_id to the data
    $validated['user_id'] = $user->id;

    try {
      $annotation = $this->annotationService->createAnnotation($validated);

      return response()->json([
        'success' => true,
        'message' => 'Annotation created successfully.',
        'data' => $annotation,
      ], 201);
    } catch (ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Validation failed.',
        'errors' => $e->errors(),
      ], 422);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to create annotation: ' . $e->getMessage(),
      ], 400);
    }
  }

  /**
   * Update an existing annotation.
   *
   * @param Request $request
   * @param Annotation $annotation
   * @return JsonResponse
   */
  public function update(Request $request, Annotation $annotation): JsonResponse
  {
    $user = Auth::user();

    if (!$user) {
      return response()->json([
        'success' => false,
        'message' => 'Unauthenticated.',
      ], 401);
    }

    // Check authorization using policy
    $this->authorize('update', $annotation);

    // Validate update fields
    $validated = $request->validate([
      'annotation_type' => 'sometimes|in:marker,highlight,discrepancy,drawing',
      'coordinates' => 'sometimes|array',
      'comment' => 'nullable|string|max:5000',
      'is_discrepancy' => 'sometimes|boolean',
    ]);

    // If changing to discrepancy, ensure comment is provided
    if (
      isset($validated['is_discrepancy']) &&
      $validated['is_discrepancy'] === true &&
      empty($validated['comment']) &&
      empty($annotation->comment)
    ) {
      return response()->json([
        'success' => false,
        'message' => 'Discrepancy annotations require a comment.',
        'errors' => ['comment' => ['Comment is required for discrepancy annotations.']],
      ], 422);
    }

    try {
      $annotation->update($validated);
      $annotation->load(['user', 'attachment']);

      return response()->json([
        'success' => true,
        'message' => 'Annotation updated successfully.',
        'data' => $annotation,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to update annotation: ' . $e->getMessage(),
      ], 400);
    }
  }

  /**
   * Delete an annotation.
   *
   * @param Annotation $annotation
   * @return JsonResponse
   */
  public function destroy(Annotation $annotation): JsonResponse
  {
    $user = Auth::user();

    if (!$user) {
      return response()->json([
        'success' => false,
        'message' => 'Unauthenticated.',
      ], 401);
    }

    // Check authorization using policy
    $this->authorize('delete', $annotation);

    try {
      $this->annotationService->deleteAnnotation($annotation->id, $user->id);

      return response()->json([
        'success' => true,
        'message' => 'Annotation deleted successfully.',
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => $e->getMessage(),
      ], $e->getMessage() === 'Unauthorized to delete this annotation.' ? 403 : 400);
    }
  }

  /**
   * Get discrepancy summary for a concept paper.
   *
   * @param ConceptPaper $conceptPaper
   * @return JsonResponse
   */
  public function discrepancies(ConceptPaper $conceptPaper): JsonResponse
  {
    // Check if user can view the concept paper
    $this->authorize('view', $conceptPaper);

    try {
      $discrepancies = $this->annotationService->getDiscrepanciesForPaper($conceptPaper->id);

      return response()->json([
        'success' => true,
        'data' => $discrepancies,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'success' => false,
        'message' => 'Failed to fetch discrepancies: ' . $e->getMessage(),
      ], 500);
    }
  }
}

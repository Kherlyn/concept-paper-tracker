<?php

namespace App\Services;

use App\Models\Annotation;
use App\Models\Attachment;
use App\Models\User;
use App\Services\Contracts\AnnotationServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AnnotationService implements AnnotationServiceInterface
{
  /**
   * Default document dimensions for coordinate validation.
   * These can be overridden by actual document dimensions.
   */
  protected const DEFAULT_MAX_WIDTH = 2000;
  protected const DEFAULT_MAX_HEIGHT = 3000;

  /**
   * Create a new annotation with validation.
   *
   * @param array $data
   * @return Annotation
   * @throws \Exception
   */
  public function createAnnotation(array $data): Annotation
  {
    // Determine if this is a discrepancy annotation
    $isDiscrepancy = $data['is_discrepancy'] ?? ($data['annotation_type'] === 'discrepancy');

    // Get appropriate validation rules
    $rules = $isDiscrepancy
      ? Annotation::discrepancyValidationRules()
      : Annotation::validationRules();

    // Validate the input data
    $validator = Validator::make($data, $rules);

    if ($validator->fails()) {
      throw ValidationException::withMessages($validator->errors()->toArray());
    }

    // Verify the attachment exists and belongs to the concept paper
    $attachment = Attachment::find($data['attachment_id']);
    if (!$attachment) {
      throw new \Exception('Attachment not found.');
    }

    // Verify the attachment belongs to the concept paper
    if (
      $attachment->attachable_type === 'App\Models\ConceptPaper' &&
      $attachment->attachable_id != $data['concept_paper_id']
    ) {
      throw new \Exception('Attachment does not belong to the specified concept paper.');
    }

    // Validate coordinates are within document bounds
    $maxWidth = $data['max_width'] ?? self::DEFAULT_MAX_WIDTH;
    $maxHeight = $data['max_height'] ?? self::DEFAULT_MAX_HEIGHT;

    if (!Annotation::validateCoordinates($data['coordinates'], $maxWidth, $maxHeight)) {
      throw new \Exception('Coordinates are outside document bounds.');
    }

    // Ensure is_discrepancy is set correctly
    if ($data['annotation_type'] === 'discrepancy') {
      $data['is_discrepancy'] = true;
    }

    // Create the annotation
    $annotation = Annotation::create([
      'concept_paper_id' => $data['concept_paper_id'],
      'attachment_id' => $data['attachment_id'],
      'user_id' => $data['user_id'],
      'page_number' => $data['page_number'],
      'annotation_type' => $data['annotation_type'],
      'coordinates' => $data['coordinates'],
      'comment' => $data['comment'] ?? null,
      'is_discrepancy' => $data['is_discrepancy'] ?? false,
    ]);

    return $annotation->load(['user', 'attachment']);
  }

  /**
   * Get annotations for a specific document with optional page filtering.
   *
   * @param int $attachmentId
   * @param int|null $pageNumber
   * @return Collection
   */
  public function getAnnotationsForDocument(int $attachmentId, ?int $pageNumber = null): Collection
  {
    $query = Annotation::where('attachment_id', $attachmentId)
      ->with(['user:id,name,email', 'attachment:id,file_name']);

    // Apply page filtering if specified
    if ($pageNumber !== null) {
      $query->where('page_number', $pageNumber);
    }

    return $query->orderBy('page_number')
      ->orderBy('created_at')
      ->get();
  }

  /**
   * Get all discrepancies for a concept paper.
   *
   * @param int $conceptPaperId
   * @return Collection
   */
  public function getDiscrepanciesForPaper(int $conceptPaperId): Collection
  {
    return Annotation::where('concept_paper_id', $conceptPaperId)
      ->where('is_discrepancy', true)
      ->with([
        'user:id,name,email',
        'attachment:id,file_name',
      ])
      ->orderBy('created_at', 'desc')
      ->get();
  }

  /**
   * Delete an annotation with authorization check.
   *
   * @param int $annotationId
   * @param int $userId
   * @return bool
   * @throws \Exception
   */
  public function deleteAnnotation(int $annotationId, int $userId): bool
  {
    $annotation = Annotation::find($annotationId);

    if (!$annotation) {
      throw new \Exception('Annotation not found.');
    }

    // Check authorization: user must be the creator or an admin
    $user = User::find($userId);

    if (!$user) {
      throw new \Exception('User not found.');
    }

    // Allow deletion if user is the creator or an admin
    if ($annotation->user_id !== $userId && !$user->hasRole('admin')) {
      throw new \Exception('Unauthorized to delete this annotation.');
    }

    return $annotation->delete();
  }
}

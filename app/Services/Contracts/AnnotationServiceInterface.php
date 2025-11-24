<?php

namespace App\Services\Contracts;

use App\Models\Annotation;
use Illuminate\Support\Collection;

interface AnnotationServiceInterface
{
  /**
   * Create a new annotation with validation.
   *
   * @param array $data
   * @return Annotation
   * @throws \Exception
   */
  public function createAnnotation(array $data): Annotation;

  /**
   * Get annotations for a specific document with optional page filtering.
   *
   * @param int $attachmentId
   * @param int|null $pageNumber
   * @return Collection
   */
  public function getAnnotationsForDocument(int $attachmentId, ?int $pageNumber = null): Collection;

  /**
   * Get all discrepancies for a concept paper.
   *
   * @param int $conceptPaperId
   * @return Collection
   */
  public function getDiscrepanciesForPaper(int $conceptPaperId): Collection;

  /**
   * Delete an annotation with authorization check.
   *
   * @param int $annotationId
   * @param int $userId
   * @return bool
   * @throws \Exception
   */
  public function deleteAnnotation(int $annotationId, int $userId): bool;
}

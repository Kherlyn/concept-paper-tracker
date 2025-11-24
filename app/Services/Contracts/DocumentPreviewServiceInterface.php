<?php

namespace App\Services\Contracts;

use App\Models\Attachment;
use Illuminate\Http\UploadedFile;

interface DocumentPreviewServiceInterface
{
  /**
   * Validate if the uploaded file is a valid Word document.
   *
   * @param UploadedFile $file
   * @return bool
   */
  public function validateWordDocument(UploadedFile $file): bool;

  /**
   * Convert a Word document to a preview format (PDF).
   *
   * @param Attachment $attachment
   * @return string|null Path to the converted file, or null on failure
   */
  public function convertToPreviewFormat(Attachment $attachment): ?string;

  /**
   * Extract the page count from a document.
   *
   * @param Attachment $attachment
   * @return int
   */
  public function extractPageCount(Attachment $attachment): int;
}

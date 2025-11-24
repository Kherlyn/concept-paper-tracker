<?php

namespace App\Services;

use App\Models\Attachment;
use App\Services\Contracts\DocumentPreviewServiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;

class DocumentPreviewService implements DocumentPreviewServiceInterface
{
  /**
   * Cache TTL for converted PDFs (24 hours in seconds).
   */
  private const CACHE_TTL = 86400;

  /**
   * Allowed Word document MIME types.
   */
  private const WORD_MIME_TYPES = [
    'application/msword', // .doc
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
  ];

  /**
   * Allowed Word document extensions.
   */
  private const WORD_EXTENSIONS = ['doc', 'docx'];

  /**
   * Validate if the uploaded file is a valid Word document.
   *
   * @param UploadedFile $file
   * @return bool
   */
  public function validateWordDocument(UploadedFile $file): bool
  {
    // Check MIME type
    $mimeType = $file->getMimeType();
    if (!in_array($mimeType, self::WORD_MIME_TYPES)) {
      return false;
    }

    // Check file extension
    $extension = strtolower($file->getClientOriginalExtension());
    if (!in_array($extension, self::WORD_EXTENSIONS)) {
      return false;
    }

    return true;
  }

  /**
   * Convert a Word document to a preview format (PDF).
   *
   * @param Attachment $attachment
   * @return string|null Path to the converted file, or null on failure
   */
  public function convertToPreviewFormat(Attachment $attachment): ?string
  {
    // Check if this is a Word document
    if (!$this->isWordDocument($attachment)) {
      // If it's a PDF, return the original path
      if ($attachment->mime_type === 'application/pdf') {
        return $attachment->file_path;
      }
      return null;
    }

    // Check cache first
    $cacheKey = "preview_pdf_{$attachment->id}";
    $cachedPath = Cache::get($cacheKey);

    if ($cachedPath && Storage::disk('concept_papers')->exists($cachedPath)) {
      return $cachedPath;
    }

    try {
      // Get the storage disk
      $storageDisk = config('upload.storage_disk', 'concept_papers');

      // Get the full path to the Word document
      $wordPath = Storage::disk($storageDisk)->path($attachment->file_path);

      if (!file_exists($wordPath)) {
        Log::error("Word document not found: {$wordPath}");
        return null;
      }

      // Load the Word document
      $phpWord = IOFactory::load($wordPath);

      // Generate HTML from Word document
      $htmlWriter = IOFactory::createWriter($phpWord, 'HTML');

      // Create a temporary file for HTML
      $tempHtmlPath = tempnam(sys_get_temp_dir(), 'word_html_');
      $htmlWriter->save($tempHtmlPath);

      // Read the HTML content
      $htmlContent = file_get_contents($tempHtmlPath);

      // Clean up temp HTML file
      unlink($tempHtmlPath);

      // Generate PDF from HTML using DomPDF
      $pdf = Pdf::loadHTML($htmlContent);
      $pdf->setPaper('A4', 'portrait');

      // Generate the PDF output
      $pdfOutput = $pdf->output();

      // Create a path for the converted PDF
      $pdfPath = $this->generatePreviewPath($attachment);

      // Save the PDF to storage
      Storage::disk($storageDisk)->put($pdfPath, $pdfOutput);

      // Cache the path for 24 hours
      Cache::put($cacheKey, $pdfPath, self::CACHE_TTL);

      Log::info("Successfully converted Word document to PDF: {$attachment->file_name}");

      return $pdfPath;
    } catch (\Exception $e) {
      Log::error("Failed to convert Word document to PDF: {$e->getMessage()}", [
        'attachment_id' => $attachment->id,
        'file_name' => $attachment->file_name,
        'exception' => $e,
      ]);

      return null;
    }
  }

  /**
   * Extract the page count from a document.
   *
   * @param Attachment $attachment
   * @return int
   */
  public function extractPageCount(Attachment $attachment): int
  {
    try {
      // For PDF files, we can use a PDF library
      if ($attachment->mime_type === 'application/pdf') {
        return $this->extractPdfPageCount($attachment);
      }

      // For Word documents, convert to PDF first and then count pages
      if ($this->isWordDocument($attachment)) {
        $pdfPath = $this->convertToPreviewFormat($attachment);

        if ($pdfPath) {
          // Create a temporary attachment object for the PDF
          $tempAttachment = new Attachment([
            'file_path' => $pdfPath,
            'mime_type' => 'application/pdf',
          ]);

          return $this->extractPdfPageCount($tempAttachment);
        }
      }

      return 1; // Default to 1 page if we can't determine

    } catch (\Exception $e) {
      Log::error("Failed to extract page count: {$e->getMessage()}", [
        'attachment_id' => $attachment->id,
        'file_name' => $attachment->file_name,
      ]);

      return 1; // Default to 1 page on error
    }
  }

  /**
   * Extract page count from a PDF file.
   *
   * @param Attachment $attachment
   * @return int
   */
  private function extractPdfPageCount(Attachment $attachment): int
  {
    try {
      $storageDisk = config('upload.storage_disk', 'concept_papers');
      $pdfPath = Storage::disk($storageDisk)->path($attachment->file_path);

      if (!file_exists($pdfPath)) {
        return 1;
      }

      // Read the PDF file content
      $content = file_get_contents($pdfPath);

      // Count pages using regex pattern for PDF page objects
      // This is a simple approach that works for most PDFs
      preg_match_all('/\/Type[\s]*\/Page[^s]/', $content, $matches);
      $pageCount = count($matches[0]);

      return max(1, $pageCount); // At least 1 page

    } catch (\Exception $e) {
      Log::error("Failed to extract PDF page count: {$e->getMessage()}");
      return 1;
    }
  }

  /**
   * Check if the attachment is a Word document.
   *
   * @param Attachment $attachment
   * @return bool
   */
  private function isWordDocument(Attachment $attachment): bool
  {
    return in_array($attachment->mime_type, self::WORD_MIME_TYPES);
  }

  /**
   * Generate a path for the preview PDF.
   *
   * @param Attachment $attachment
   * @return string
   */
  private function generatePreviewPath(Attachment $attachment): string
  {
    $pathInfo = pathinfo($attachment->file_path);
    $directory = $pathInfo['dirname'];
    $filename = $pathInfo['filename'];

    return "{$directory}/{$filename}_preview.pdf";
  }
}

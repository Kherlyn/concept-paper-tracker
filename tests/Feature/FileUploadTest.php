<?php

use App\Models\User;
use App\Models\ConceptPaper;
use App\Models\Attachment;
use App\Services\ConceptPaperService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
  Storage::fake('concept_papers');
});

test('can attach file to concept paper with validation', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $service = app(ConceptPaperService::class);

  // Create a fake PDF file
  $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');

  $attachment = $service->attachFile($conceptPaper, $file, $user);

  expect($attachment)->toBeInstanceOf(Attachment::class);
  expect($attachment->file_name)->toBe('test-document.pdf');
  expect($attachment->mime_type)->toBe('application/pdf');
  expect($attachment->uploaded_by)->toBe($user->id);

  // Verify file was stored
  Storage::disk('concept_papers')->assertExists($attachment->file_path);
});

test('rejects non-pdf files', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $service = app(ConceptPaperService::class);

  // Create a fake non-PDF file
  $file = UploadedFile::fake()->create('test-document.txt', 1024, 'text/plain');

  $service->attachFile($conceptPaper, $file, $user);
})->throws(Exception::class, 'Only PDF files are allowed.');

test('rejects files exceeding size limit', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $service = app(ConceptPaperService::class);

  // Create a fake PDF file larger than 10MB
  $file = UploadedFile::fake()->create('large-document.pdf', 11000, 'application/pdf');

  $service->attachFile($conceptPaper, $file, $user);
})->throws(Exception::class);

test('deletes physical file when attachment is deleted', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $service = app(ConceptPaperService::class);

  $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');
  $attachment = $service->attachFile($conceptPaper, $file, $user);

  $filePath = $attachment->file_path;

  // Verify file exists
  Storage::disk('concept_papers')->assertExists($filePath);

  // Delete attachment
  $attachment->delete();

  // Verify file was deleted
  Storage::disk('concept_papers')->assertMissing($filePath);
});

test('deletes all attachments when concept paper is deleted', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $service = app(ConceptPaperService::class);

  // Upload multiple files
  $file1 = UploadedFile::fake()->create('document1.pdf', 1024, 'application/pdf');
  $file2 = UploadedFile::fake()->create('document2.pdf', 1024, 'application/pdf');

  $attachment1 = $service->attachFile($conceptPaper, $file1, $user);
  $attachment2 = $service->attachFile($conceptPaper, $file2, $user);

  $filePath1 = $attachment1->file_path;
  $filePath2 = $attachment2->file_path;

  // Verify files exist
  Storage::disk('concept_papers')->assertExists($filePath1);
  Storage::disk('concept_papers')->assertExists($filePath2);

  // Delete concept paper
  $conceptPaper->delete();

  // Verify all files were deleted
  Storage::disk('concept_papers')->assertMissing($filePath1);
  Storage::disk('concept_papers')->assertMissing($filePath2);

  // Verify attachment records were deleted
  expect(Attachment::find($attachment1->id))->toBeNull();
  expect(Attachment::find($attachment2->id))->toBeNull();
});

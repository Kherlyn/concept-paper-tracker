<?php

use App\Models\Attachment;
use App\Models\ConceptPaper;
use App\Models\User;
use App\Services\DocumentPreviewService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
  $this->service = app(DocumentPreviewService::class);
  Storage::fake('concept_papers');
});

test('validateWordDocument accepts valid .docx files', function () {
  $file = UploadedFile::fake()->create('document.docx', 100, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

  $result = $this->service->validateWordDocument($file);

  expect($result)->toBeTrue();
});

test('validateWordDocument accepts valid .doc files', function () {
  $file = UploadedFile::fake()->create('document.doc', 100, 'application/msword');

  $result = $this->service->validateWordDocument($file);

  expect($result)->toBeTrue();
});

test('validateWordDocument rejects PDF files', function () {
  $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

  $result = $this->service->validateWordDocument($file);

  expect($result)->toBeFalse();
});

test('validateWordDocument rejects files with wrong extension', function () {
  $file = UploadedFile::fake()->create('document.txt', 100, 'text/plain');

  $result = $this->service->validateWordDocument($file);

  expect($result)->toBeFalse();
});

test('convertToPreviewFormat returns original path for PDF files', function () {
  // Create a test user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create a fake PDF file
  $pdfContent = '%PDF-1.4 fake pdf content';
  Storage::disk('concept_papers')->put('test.pdf', $pdfContent);

  // Create an attachment record
  $attachment = Attachment::create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
    'file_name' => 'test.pdf',
    'file_path' => 'test.pdf',
    'file_size' => strlen($pdfContent),
    'mime_type' => 'application/pdf',
    'uploaded_by' => $user->id,
  ]);

  $result = $this->service->convertToPreviewFormat($attachment);

  expect($result)->toBe('test.pdf');
});

test('convertToPreviewFormat returns null for unsupported file types', function () {
  // Create a test user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create a fake text file
  Storage::disk('concept_papers')->put('test.txt', 'some text content');

  // Create an attachment record
  $attachment = Attachment::create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
    'file_name' => 'test.txt',
    'file_path' => 'test.txt',
    'file_size' => 100,
    'mime_type' => 'text/plain',
    'uploaded_by' => $user->id,
  ]);

  $result = $this->service->convertToPreviewFormat($attachment);

  expect($result)->toBeNull();
});

test('extractPageCount returns at least 1 for any document', function () {
  // Create a test user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create a fake PDF file
  $pdfContent = '%PDF-1.4 fake pdf content';
  Storage::disk('concept_papers')->put('test.pdf', $pdfContent);

  // Create an attachment record
  $attachment = Attachment::create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
    'file_name' => 'test.pdf',
    'file_path' => 'test.pdf',
    'file_size' => strlen($pdfContent),
    'mime_type' => 'application/pdf',
    'uploaded_by' => $user->id,
  ]);

  $result = $this->service->extractPageCount($attachment);

  expect($result)->toBeGreaterThanOrEqual(1);
});

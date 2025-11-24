<?php

use App\Models\Attachment;
use App\Models\ConceptPaper;
use App\Models\User;
use App\Services\Contracts\DocumentPreviewServiceInterface;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
  Storage::fake('concept_papers');
});

test('preview returns PDF for PDF attachments', function () {
  // Create a user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create a PDF file
  $pdfContent = '%PDF-1.4 test content';
  $filePath = 'test/document.pdf';
  Storage::disk('concept_papers')->put($filePath, $pdfContent);

  // Create an attachment
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => $filePath,
    'file_name' => 'document.pdf',
    'mime_type' => 'application/pdf',
  ]);

  // Act as the user and request preview
  $response = $this->actingAs($user)->get(route('attachments.preview', $attachment));

  // Assert
  $response->assertStatus(200);
  $response->assertHeader('Content-Type', 'application/pdf');
  $response->assertHeader('Content-Disposition', 'inline; filename="document_preview.pdf"');
});

test('preview converts Word documents to PDF', function () {
  // Create a user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create a mock Word file
  $wordContent = 'Mock Word document content';
  $filePath = 'test/document.docx';
  Storage::disk('concept_papers')->put($filePath, $wordContent);

  // Create an attachment
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => $filePath,
    'file_name' => 'document.docx',
    'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  ]);

  // Mock the preview service to return a converted path
  $previewPath = 'test/document_preview.pdf';
  $pdfContent = '%PDF-1.4 converted content';
  Storage::disk('concept_papers')->put($previewPath, $pdfContent);

  $mockService = Mockery::mock(DocumentPreviewServiceInterface::class);
  $mockService->shouldReceive('convertToPreviewFormat')
    ->once()
    ->with(Mockery::on(fn($arg) => $arg->id === $attachment->id))
    ->andReturn($previewPath);

  $this->app->instance(DocumentPreviewServiceInterface::class, $mockService);

  // Act as the user and request preview
  $response = $this->actingAs($user)->get(route('attachments.preview', $attachment));

  // Assert
  $response->assertStatus(200);
  $response->assertHeader('Content-Type', 'application/pdf');
});

test('preview returns error when conversion fails', function () {
  // Create a user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create a mock Word file
  $wordContent = 'Mock Word document content';
  $filePath = 'test/document.docx';
  Storage::disk('concept_papers')->put($filePath, $wordContent);

  // Create an attachment
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => $filePath,
    'file_name' => 'document.docx',
    'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  ]);

  // Mock the preview service to return null (conversion failed)
  $mockService = Mockery::mock(DocumentPreviewServiceInterface::class);
  $mockService->shouldReceive('convertToPreviewFormat')
    ->once()
    ->andReturn(null);

  $this->app->instance(DocumentPreviewServiceInterface::class, $mockService);

  // Act as the user and request preview
  $response = $this->actingAs($user)->get(route('attachments.preview', $attachment));

  // Assert
  $response->assertStatus(422);
  $response->assertJson([
    'error' => 'Preview unavailable. Please download the file instead.',
  ]);
});

test('preview requires authorization', function () {
  // Create two users
  $owner = User::factory()->create(['role' => 'requisitioner']);
  $otherUser = User::factory()->create(['role' => 'requisitioner']);

  // Create a concept paper owned by the first user
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $owner->id]);

  // Create an attachment
  $filePath = 'test/document.pdf';
  Storage::disk('concept_papers')->put($filePath, '%PDF-1.4 test');

  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => $filePath,
    'file_name' => 'document.pdf',
    'mime_type' => 'application/pdf',
  ]);

  // Try to access as the other user (should be forbidden)
  $response = $this->actingAs($otherUser)->get(route('attachments.preview', $attachment));

  // Assert
  $response->assertStatus(403);
});

test('download returns original file', function () {
  // Create a user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create a file
  $fileContent = 'Test file content';
  $filePath = 'test/document.pdf';
  Storage::disk('concept_papers')->put($filePath, $fileContent);

  // Create an attachment
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => $filePath,
    'file_name' => 'document.pdf',
    'mime_type' => 'application/pdf',
  ]);

  // Act as the user and request download
  $response = $this->actingAs($user)->get(route('attachments.download', $attachment));

  // Assert
  $response->assertStatus(200);
  $response->assertHeader('Content-Type', 'application/pdf');
  $response->assertDownload('document.pdf');
});

test('download requires authorization', function () {
  // Create two users
  $owner = User::factory()->create(['role' => 'requisitioner']);
  $otherUser = User::factory()->create(['role' => 'requisitioner']);

  // Create a concept paper owned by the first user
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $owner->id]);

  // Create an attachment
  $filePath = 'test/document.pdf';
  Storage::disk('concept_papers')->put($filePath, 'test content');

  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => $filePath,
    'file_name' => 'document.pdf',
    'mime_type' => 'application/pdf',
  ]);

  // Try to access as the other user (should be forbidden)
  $response = $this->actingAs($otherUser)->get(route('attachments.download', $attachment));

  // Assert
  $response->assertStatus(403);
});

test('preview returns 404 when file not found', function () {
  // Create a user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create an attachment with non-existent file
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => 'nonexistent/file.pdf',
    'file_name' => 'document.pdf',
    'mime_type' => 'application/pdf',
  ]);

  // Act as the user and request preview
  $response = $this->actingAs($user)->get(route('attachments.preview', $attachment));

  // Assert
  $response->assertStatus(404);
});

test('download returns 404 when file not found', function () {
  // Create a user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create an attachment with non-existent file
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => 'nonexistent/file.pdf',
    'file_name' => 'document.pdf',
    'mime_type' => 'application/pdf',
  ]);

  // Act as the user and request download
  $response = $this->actingAs($user)->get(route('attachments.download', $attachment));

  // Assert
  $response->assertStatus(404);
});

test('preview includes caching headers', function () {
  // Create a user and concept paper
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create a PDF file
  $pdfContent = '%PDF-1.4 test content';
  $filePath = 'test/document.pdf';
  Storage::disk('concept_papers')->put($filePath, $pdfContent);

  // Create an attachment
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_path' => $filePath,
    'file_name' => 'document.pdf',
    'mime_type' => 'application/pdf',
  ]);

  // Act as the user and request preview
  $response = $this->actingAs($user)->get(route('attachments.preview', $attachment));

  // Assert caching headers
  $response->assertStatus(200);
  $response->assertHeader('Cache-Control', 'max-age=86400, public');
  expect($response->headers->has('Expires'))->toBeTrue();
});

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

test('authorized user can download attachment', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $service = app(ConceptPaperService::class);
  $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');
  $attachment = $service->attachFile($conceptPaper, $file, $user);

  $response = $this->actingAs($user)->get(route('attachments.download', $attachment));

  $response->assertOk();
  $response->assertHeader('content-type', 'application/pdf');
});

test('unauthorized user cannot download attachment', function () {
  $owner = User::factory()->create(['role' => 'requisitioner']);
  $otherUser = User::factory()->create(['role' => 'requisitioner']);

  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $owner->id]);

  $service = app(ConceptPaperService::class);
  $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');
  $attachment = $service->attachFile($conceptPaper, $file, $owner);

  $response = $this->actingAs($otherUser)->get(route('attachments.download', $attachment));

  $response->assertForbidden();
});

test('admin can download any attachment', function () {
  $owner = User::factory()->create(['role' => 'requisitioner']);
  $admin = User::factory()->create(['role' => 'admin']);

  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $owner->id]);

  $service = app(ConceptPaperService::class);
  $file = UploadedFile::fake()->create('test-document.pdf', 1024, 'application/pdf');
  $attachment = $service->attachFile($conceptPaper, $file, $owner);

  $response = $this->actingAs($admin)->get(route('attachments.download', $attachment));

  $response->assertOk();
});

test('returns 404 when file does not exist', function () {
  $user = User::factory()->create(['role' => 'requisitioner']);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  // Create attachment record without actual file
  $attachment = Attachment::create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $conceptPaper->id,
    'file_name' => 'missing.pdf',
    'file_path' => 'nonexistent/path/missing.pdf',
    'file_size' => 1024,
    'mime_type' => 'application/pdf',
    'uploaded_by' => $user->id,
  ]);

  $response = $this->actingAs($user)->get(route('attachments.download', $attachment));

  $response->assertNotFound();
});

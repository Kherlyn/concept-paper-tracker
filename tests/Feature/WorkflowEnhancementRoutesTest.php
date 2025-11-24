<?php

use App\Models\User;
use App\Models\ConceptPaper;
use App\Models\WorkflowStage;
use App\Models\Attachment;
use App\Models\Annotation;
use App\Models\DeadlineOption;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
  Storage::fake('local');
});

// ============================================================================
// User Activation Management Routes
// ============================================================================

test('admin can toggle user activation status', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $user = User::factory()->create(['role' => 'sps', 'is_active' => true]);

  $response = $this->actingAs($admin)->patch(route('admin.users.toggle-activation', $user));

  $response->assertStatus(200);
  $this->assertDatabaseHas('users', [
    'id' => $user->id,
    'is_active' => false,
  ]);
});

test('non-admin cannot toggle user activation status', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $targetUser = User::factory()->create(['role' => 'sps', 'is_active' => true]);

  $response = $this->actingAs($user)->patch(route('admin.users.toggle-activation', $targetUser));

  $response->assertStatus(403);
});

test('admin can get assigned stages for a user', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $user = User::factory()->create(['role' => 'sps', 'is_active' => true]);

  $conceptPaper = ConceptPaper::factory()->create();
  WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'assigned_user_id' => $user->id,
    'status' => 'pending',
  ]);

  $response = $this->actingAs($admin)->get(route('admin.users.assigned-stages', $user));

  $response->assertStatus(200);
  $response->assertJsonStructure([
    'stages' => [
      '*' => ['id', 'concept_paper_id', 'stage_name', 'status']
    ]
  ]);
});

test('admin can reassign a workflow stage', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $oldUser = User::factory()->create(['role' => 'sps', 'is_active' => true]);
  $newUser = User::factory()->create(['role' => 'sps', 'is_active' => true]);

  $conceptPaper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'assigned_user_id' => $oldUser->id,
    'status' => 'pending',
  ]);

  $response = $this->actingAs($admin)->post(route('admin.stages.reassign', $stage), [
    'new_user_id' => $newUser->id,
  ]);

  $response->assertStatus(200);
  $this->assertDatabaseHas('workflow_stages', [
    'id' => $stage->id,
    'assigned_user_id' => $newUser->id,
  ]);
});

// ============================================================================
// Annotation CRUD Routes
// ============================================================================

test('authenticated user can create annotation on accessible concept paper', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_id' => $conceptPaper->id,
    'attachable_type' => ConceptPaper::class,
  ]);

  $response = $this->actingAs($user)->post(route('annotations.store'), [
    'concept_paper_id' => $conceptPaper->id,
    'attachment_id' => $attachment->id,
    'page_number' => 1,
    'annotation_type' => 'marker',
    'coordinates' => ['x' => 100, 'y' => 150, 'width' => 50, 'height' => 30],
    'comment' => 'Test annotation',
    'is_discrepancy' => false,
  ]);

  $response->assertStatus(201);
  $this->assertDatabaseHas('annotations', [
    'concept_paper_id' => $conceptPaper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
    'comment' => 'Test annotation',
  ]);
});

test('authenticated user can get annotations for a document', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_id' => $conceptPaper->id,
    'attachable_type' => ConceptPaper::class,
  ]);

  Annotation::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
    'page_number' => 1,
  ]);

  $response = $this->actingAs($user)->get(route('annotations.index', [
    'attachment_id' => $attachment->id,
    'page_number' => 1,
  ]));

  $response->assertStatus(200);
});

test('user can update their own annotation', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_id' => $conceptPaper->id,
    'attachable_type' => ConceptPaper::class,
  ]);

  $annotation = Annotation::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
    'comment' => 'Original comment',
  ]);

  $response = $this->actingAs($user)->put(route('annotations.update', $annotation), [
    'comment' => 'Updated comment',
    'coordinates' => ['x' => 200, 'y' => 250, 'width' => 60, 'height' => 40],
  ]);

  $response->assertStatus(200);
  $this->assertDatabaseHas('annotations', [
    'id' => $annotation->id,
    'comment' => 'Updated comment',
  ]);
});

test('user can delete their own annotation', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_id' => $conceptPaper->id,
    'attachable_type' => ConceptPaper::class,
  ]);

  $annotation = Annotation::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
  ]);

  $response = $this->actingAs($user)->delete(route('annotations.destroy', $annotation));

  $response->assertStatus(200);
  $this->assertDatabaseMissing('annotations', [
    'id' => $annotation->id,
  ]);
});

// ============================================================================
// Document Preview Routes
// ============================================================================

test('authenticated user can preview their own attachment', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  Storage::fake('local');
  $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
  $filePath = $file->store('concept_papers', 'local');
  Storage::disk('local')->put($filePath, 'test content');

  $attachment = Attachment::factory()->create([
    'attachable_id' => $conceptPaper->id,
    'attachable_type' => ConceptPaper::class,
    'file_path' => $filePath,
    'mime_type' => 'application/pdf',
  ]);

  $response = $this->actingAs($user)->get(route('attachments.preview', $attachment));

  $response->assertStatus(200);
});

test('authenticated user can download their own attachment', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  Storage::fake('local');
  $file = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');
  $filePath = $file->store('concept_papers', 'local');
  Storage::disk('local')->put($filePath, 'test content');

  $attachment = Attachment::factory()->create([
    'attachable_id' => $conceptPaper->id,
    'attachable_type' => ConceptPaper::class,
    'file_path' => $filePath,
    'mime_type' => 'application/pdf',
  ]);

  $response = $this->actingAs($user)->get(route('attachments.download', $attachment));

  $response->assertStatus(200);
});

// ============================================================================
// Discrepancy Summary Routes
// ============================================================================

test('authenticated user can get discrepancies for their concept paper', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $conceptPaper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_id' => $conceptPaper->id,
    'attachable_type' => ConceptPaper::class,
  ]);

  Annotation::factory()->create([
    'concept_paper_id' => $conceptPaper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
    'is_discrepancy' => true,
    'comment' => 'This is a discrepancy',
  ]);

  $response = $this->actingAs($user)->get(route('concept-papers.discrepancies', $conceptPaper));

  $response->assertStatus(200);
  $response->assertJsonStructure([
    'discrepancies' => [
      '*' => ['id', 'comment', 'page_number', 'user', 'attachment']
    ]
  ]);
});

// ============================================================================
// Deadline Option Routes
// ============================================================================

test('authenticated user can get deadline options', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $response = $this->actingAs($user)->get(route('deadline-options.index'));

  $response->assertStatus(200);
  $response->assertJsonStructure([
    'deadline_options'
  ]);
});

test('admin can create deadline option', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);

  $response = $this->actingAs($admin)->post(route('admin.deadline-options.store'), [
    'key' => '6_months',
    'label' => '6 Months',
    'days' => 180,
    'sort_order' => 10,
  ]);

  $response->assertStatus(201);
  $this->assertDatabaseHas('deadline_options', [
    'key' => '6_months',
    'label' => '6 Months',
    'days' => 180,
  ]);
});

test('non-admin cannot create deadline option', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $response = $this->actingAs($user)->post(route('admin.deadline-options.store'), [
    'key' => '6_months',
    'label' => '6 Months',
    'days' => 180,
    'sort_order' => 10,
  ]);

  $response->assertStatus(403);
});

test('admin can update deadline option', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $option = DeadlineOption::factory()->create(['key' => 'test_week', 'label' => '1 Week', 'days' => 7]);

  $response = $this->actingAs($admin)->put(route('admin.deadline-options.update', 'test_week'), [
    'label' => 'One Week',
    'days' => 7,
    'sort_order' => 1,
  ]);

  $response->assertStatus(200);
  $this->assertDatabaseHas('deadline_options', [
    'key' => 'test_week',
    'label' => 'One Week',
  ]);
});

test('admin can delete deadline option', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $option = DeadlineOption::factory()->create(['key' => 'test_delete', 'label' => '1 Week', 'days' => 7]);

  $response = $this->actingAs($admin)->delete(route('admin.deadline-options.destroy', 'test_delete'));

  $response->assertStatus(200);
  $this->assertDatabaseMissing('deadline_options', [
    'key' => 'test_delete',
  ]);
});

// ============================================================================
// Authorization Tests
// ============================================================================

test('unauthenticated user cannot access protected routes', function () {
  $response = $this->get(route('dashboard'));

  $response->assertRedirect(route('login'));
});

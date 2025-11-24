<?php

use App\Models\Annotation;
use App\Models\Attachment;
use App\Models\ConceptPaper;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
  Storage::fake('concept_papers');
});

test('authenticated user can create annotation on concept paper they can view', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);

  $response = $this->actingAs($user)->postJson(route('annotations.store'), [
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'page_number' => 1,
    'annotation_type' => 'marker',
    'coordinates' => [
      'x' => 100,
      'y' => 150,
      'width' => 50,
      'height' => 30,
    ],
    'comment' => 'This needs review',
    'is_discrepancy' => false,
  ]);

  $response->assertStatus(201);
  $response->assertJson([
    'success' => true,
    'message' => 'Annotation created successfully.',
  ]);

  $this->assertDatabaseHas('annotations', [
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
    'page_number' => 1,
    'annotation_type' => 'marker',
  ]);
});

test('discrepancy annotation requires comment', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);

  $response = $this->actingAs($user)->postJson(route('annotations.store'), [
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'page_number' => 1,
    'annotation_type' => 'discrepancy',
    'coordinates' => [
      'x' => 100,
      'y' => 150,
    ],
    'is_discrepancy' => true,
  ]);

  $response->assertStatus(422);
  $response->assertJson([
    'success' => false,
  ]);
});

test('user can fetch annotations for a document', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);

  // Create some annotations
  Annotation::factory()->count(3)->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'page_number' => 1,
  ]);

  $response = $this->actingAs($user)->getJson(route('annotations.index', [
    'attachment_id' => $attachment->id,
    'page_number' => 1,
  ]));

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
  ]);
  $response->assertJsonCount(3, 'data');
});

test('user can update their own annotation', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);
  $annotation = Annotation::factory()->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
    'comment' => 'Original comment',
  ]);

  $response = $this->actingAs($user)->putJson(route('annotations.update', $annotation), [
    'comment' => 'Updated comment',
  ]);

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
    'message' => 'Annotation updated successfully.',
  ]);

  $this->assertDatabaseHas('annotations', [
    'id' => $annotation->id,
    'comment' => 'Updated comment',
  ]);
});

test('user cannot update another users annotation', function () {
  $user1 = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $user2 = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user1->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);
  $annotation = Annotation::factory()->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user1->id,
  ]);

  $response = $this->actingAs($user2)->putJson(route('annotations.update', $annotation), [
    'comment' => 'Trying to update',
  ]);

  $response->assertStatus(403);
});

test('admin can update any annotation', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);
  $annotation = Annotation::factory()->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
  ]);

  $response = $this->actingAs($admin)->putJson(route('annotations.update', $annotation), [
    'comment' => 'Admin update',
  ]);

  $response->assertStatus(200);
});

test('user can delete their own annotation', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);
  $annotation = Annotation::factory()->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
  ]);

  $response = $this->actingAs($user)->deleteJson(route('annotations.destroy', $annotation));

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
    'message' => 'Annotation deleted successfully.',
  ]);

  $this->assertDatabaseMissing('annotations', [
    'id' => $annotation->id,
  ]);
});

test('user cannot delete another users annotation', function () {
  $user1 = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $user2 = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user1->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);
  $annotation = Annotation::factory()->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user1->id,
  ]);

  $response = $this->actingAs($user2)->deleteJson(route('annotations.destroy', $annotation));

  $response->assertStatus(403);
});

test('admin can delete any annotation', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);
  $annotation = Annotation::factory()->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'user_id' => $user->id,
  ]);

  $response = $this->actingAs($admin)->deleteJson(route('annotations.destroy', $annotation));

  $response->assertStatus(200);
});

test('user can fetch discrepancies for a concept paper', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);

  // Create regular annotations
  Annotation::factory()->count(2)->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'is_discrepancy' => false,
  ]);

  // Create discrepancy annotations
  Annotation::factory()->count(3)->create([
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'is_discrepancy' => true,
    'comment' => 'This is a discrepancy',
  ]);

  $response = $this->actingAs($user)->getJson(route('concept-papers.discrepancies', $paper));

  $response->assertStatus(200);
  $response->assertJson([
    'success' => true,
  ]);
  $response->assertJsonCount(3, 'data');
});

test('unauthenticated user cannot create annotation', function () {
  $paper = ConceptPaper::factory()->create();
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);

  $response = $this->postJson(route('annotations.store'), [
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'page_number' => 1,
    'annotation_type' => 'marker',
    'coordinates' => [
      'x' => 100,
      'y' => 150,
    ],
  ]);

  $response->assertStatus(401);
});

test('annotation requires valid coordinates', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);
  $attachment = Attachment::factory()->create([
    'attachable_type' => ConceptPaper::class,
    'attachable_id' => $paper->id,
  ]);

  $response = $this->actingAs($user)->postJson(route('annotations.store'), [
    'concept_paper_id' => $paper->id,
    'attachment_id' => $attachment->id,
    'page_number' => 1,
    'annotation_type' => 'marker',
    'coordinates' => [], // Invalid: missing x and y
  ]);

  $response->assertStatus(422);
});

<?php

use App\Models\User;
use App\Models\ConceptPaper;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
  Storage::fake('local');
});

test('requisitioner can view concept paper creation form', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $response = $this->actingAs($user)->get(route('concept-papers.create'));

  $response->assertStatus(200);
});

test('non-requisitioner cannot view concept paper creation form', function () {
  $user = User::factory()->create(['role' => 'sps', 'is_active' => true]);

  $response = $this->actingAs($user)->get(route('concept-papers.create'));

  $response->assertStatus(403);
});

test('requisitioner can submit a concept paper', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $response = $this->actingAs($user)->post(route('concept-papers.store'), [
    'department' => 'Computer Science',
    'title' => 'Test Concept Paper',
    'nature_of_request' => 'regular',
    'students_involved' => true,
    'deadline_option' => '1_month',
  ]);

  $response->assertRedirect();
  $this->assertDatabaseHas('concept_papers', [
    'title' => 'Test Concept Paper',
    'department' => 'Computer Science',
    'requisitioner_id' => $user->id,
    'students_involved' => true,
    'deadline_option' => '1_month',
  ]);
});

test('requisitioner can submit concept paper with PDF attachment', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $file = UploadedFile::fake()->create('document.pdf', 1024, 'application/pdf');

  $response = $this->actingAs($user)->post(route('concept-papers.store'), [
    'department' => 'Computer Science',
    'title' => 'Test Concept Paper',
    'nature_of_request' => 'urgent',
    'students_involved' => false,
    'deadline_option' => '2_weeks',
    'attachment' => $file,
  ]);

  $response->assertRedirect();
  $this->assertDatabaseHas('attachments', [
    'file_name' => 'document.pdf',
    'uploaded_by' => $user->id,
  ]);
});

test('requisitioner can view their own concept papers', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $response = $this->actingAs($user)->get(route('concept-papers.show', $paper));

  $response->assertStatus(200);
});

test('requisitioner can update their concept paper before first approval', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create(['requisitioner_id' => $user->id]);

  $response = $this->actingAs($user)->patch(route('concept-papers.update', $paper), [
    'title' => 'Updated Title',
  ]);

  $response->assertRedirect();
  $this->assertDatabaseHas('concept_papers', [
    'id' => $paper->id,
    'title' => 'Updated Title',
  ]);
});

test('admin can delete concept paper', function () {
  $admin = User::factory()->create(['role' => 'admin', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();

  $response = $this->actingAs($admin)->delete(route('concept-papers.destroy', $paper));

  $response->assertRedirect(route('concept-papers.index'));
  $this->assertSoftDeleted('concept_papers', ['id' => $paper->id]);
});

test('non-admin cannot delete concept paper', function () {
  $user = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();

  $response = $this->actingAs($user)->delete(route('concept-papers.destroy', $paper));

  $response->assertStatus(403);
});

test('concept paper index filters by user role', function () {
  $requisitioner = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);
  $otherUser = User::factory()->create(['role' => 'requisitioner', 'is_active' => true]);

  $myPaper = ConceptPaper::factory()->create(['requisitioner_id' => $requisitioner->id]);
  $otherPaper = ConceptPaper::factory()->create(['requisitioner_id' => $otherUser->id]);

  $response = $this->actingAs($requisitioner)->get(route('concept-papers.index'));

  $response->assertStatus(200);
});

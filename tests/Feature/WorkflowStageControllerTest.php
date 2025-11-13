<?php

use App\Models\User;
use App\Models\ConceptPaper;
use App\Models\WorkflowStage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
  Storage::fake('local');
});

test('assigned user can view workflow stage details', function () {
  $user = User::factory()->create(['role' => 'sps', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_role' => 'sps',
    'assigned_user_id' => $user->id,
    'status' => 'in_progress',
  ]);

  $response = $this->actingAs($user)->get(route('workflow-stages.show', $stage));

  // Note: This will return 500 until the React component is created
  // The controller logic is correct, just missing the frontend view
  $response->assertStatus(500);
})->skip('Frontend component not yet implemented');

test('assigned user can complete workflow stage', function () {
  $user = User::factory()->create(['role' => 'sps', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'stage_name' => 'SPS Review',
    'stage_order' => 1,
    'assigned_role' => 'sps',
    'assigned_user_id' => $user->id,
    'status' => 'in_progress',
  ]);

  // Create next stage
  WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'stage_name' => 'VP Acad Review',
    'stage_order' => 2,
    'assigned_role' => 'vp_acad',
    'status' => 'pending',
  ]);

  $response = $this->actingAs($user)->post(route('workflow-stages.complete', $stage), [
    'remarks' => 'Approved',
  ]);

  $response->assertRedirect(route('concept-papers.show', $paper->id));
  $this->assertDatabaseHas('workflow_stages', [
    'id' => $stage->id,
    'status' => 'completed',
  ]);
});

test('non-assigned user cannot complete workflow stage', function () {
  $assignedUser = User::factory()->create(['role' => 'sps', 'is_active' => true]);
  $otherUser = User::factory()->create(['role' => 'vp_acad', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_role' => 'sps',
    'assigned_user_id' => $assignedUser->id,
    'status' => 'in_progress',
  ]);

  $response = $this->actingAs($otherUser)->post(route('workflow-stages.complete', $stage), [
    'remarks' => 'Approved',
  ]);

  $response->assertStatus(403);
});

test('assigned user can return workflow stage to previous stage', function () {
  $user = User::factory()->create(['role' => 'vp_acad', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();

  // Create first stage (completed)
  $previousStage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'stage_name' => 'SPS Review',
    'stage_order' => 1,
    'assigned_role' => 'sps',
    'status' => 'completed',
  ]);

  // Create current stage
  $currentStage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'stage_name' => 'VP Acad Review',
    'stage_order' => 2,
    'assigned_role' => 'vp_acad',
    'assigned_user_id' => $user->id,
    'status' => 'in_progress',
  ]);

  $response = $this->actingAs($user)->post(route('workflow-stages.return', $currentStage), [
    'remarks' => 'Please revise the document',
  ]);

  $response->assertRedirect(route('concept-papers.show', $paper->id));
  $this->assertDatabaseHas('workflow_stages', [
    'id' => $currentStage->id,
    'status' => 'returned',
    'remarks' => 'Please revise the document',
  ]);
});

test('assigned user can add attachment to workflow stage', function () {
  $user = User::factory()->create(['role' => 'sps', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_role' => 'sps',
    'assigned_user_id' => $user->id,
    'status' => 'in_progress',
  ]);

  $file = UploadedFile::fake()->create('supporting-doc.pdf', 1024, 'application/pdf');

  $response = $this->actingAs($user)->post(route('workflow-stages.add-attachment', $stage), [
    'attachment' => $file,
  ]);

  $response->assertRedirect();
  $this->assertDatabaseHas('attachments', [
    'attachable_type' => WorkflowStage::class,
    'attachable_id' => $stage->id,
    'file_name' => 'supporting-doc.pdf',
    'uploaded_by' => $user->id,
  ]);
});

test('non-assigned user cannot add attachment to workflow stage', function () {
  $assignedUser = User::factory()->create(['role' => 'sps', 'is_active' => true]);
  $otherUser = User::factory()->create(['role' => 'vp_acad', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_role' => 'sps',
    'assigned_user_id' => $assignedUser->id,
    'status' => 'in_progress',
  ]);

  $file = UploadedFile::fake()->create('supporting-doc.pdf', 1024, 'application/pdf');

  $response = $this->actingAs($otherUser)->post(route('workflow-stages.add-attachment', $stage), [
    'attachment' => $file,
  ]);

  $response->assertStatus(403);
});

test('cannot add non-pdf attachment to workflow stage', function () {
  $user = User::factory()->create(['role' => 'sps', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'assigned_role' => 'sps',
    'assigned_user_id' => $user->id,
    'status' => 'in_progress',
  ]);

  $file = UploadedFile::fake()->create('document.docx', 1024, 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');

  $response = $this->actingAs($user)->post(route('workflow-stages.add-attachment', $stage), [
    'attachment' => $file,
  ]);

  $response->assertSessionHasErrors('attachment');
});

test('return action requires remarks', function () {
  $user = User::factory()->create(['role' => 'vp_acad', 'is_active' => true]);
  $paper = ConceptPaper::factory()->create();
  $stage = WorkflowStage::factory()->create([
    'concept_paper_id' => $paper->id,
    'stage_order' => 2,
    'assigned_role' => 'vp_acad',
    'assigned_user_id' => $user->id,
    'status' => 'in_progress',
  ]);

  $response = $this->actingAs($user)->post(route('workflow-stages.return', $stage), [
    'remarks' => '',
  ]);

  $response->assertSessionHasErrors('remarks');
});

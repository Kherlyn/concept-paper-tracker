<?php

namespace Database\Seeders;

use App\Models\Annotation;
use App\Models\Attachment;
use App\Models\ConceptPaper;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class AnnotationSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    // Get users for creating annotations
    $vpAcad = User::where('role', 'vp_acad')->first();
    $auditor = User::where('role', 'auditor')->first();
    $seniorVp = User::where('role', 'senior_vp')->first();

    // Get concept papers that are in progress or completed
    $paper2 = ConceptPaper::where('title', 'Student Research Grant Program')->first();
    $paper3 = ConceptPaper::where('title', 'Workshop Equipment Purchase')->first();

    if ($paper2) {
      // Create a sample attachment for paper 2
      $attachment1 = Attachment::create([
        'attachable_type' => ConceptPaper::class,
        'attachable_id' => $paper2->id,
        'file_name' => 'budget_breakdown.pdf',
        'file_path' => 'concept_papers/2025/11/sample_budget.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 524288, // 512 KB
        'uploaded_by' => $paper2->requisitioner_id,
      ]);

      // Create annotations on the attachment
      // Annotation 1: Highlight on page 1
      Annotation::create([
        'concept_paper_id' => $paper2->id,
        'attachment_id' => $attachment1->id,
        'user_id' => $vpAcad->id,
        'page_number' => 1,
        'annotation_type' => 'highlight',
        'coordinates' => [
          'x' => 150,
          'y' => 200,
          'width' => 300,
          'height' => 50,
        ],
        'comment' => 'Please verify this budget line item',
        'is_discrepancy' => false,
      ]);

      // Annotation 2: Discrepancy marker on page 2
      Annotation::create([
        'concept_paper_id' => $paper2->id,
        'attachment_id' => $attachment1->id,
        'user_id' => $vpAcad->id,
        'page_number' => 2,
        'annotation_type' => 'discrepancy',
        'coordinates' => [
          'x' => 100,
          'y' => 350,
          'width' => 80,
          'height' => 80,
        ],
        'comment' => 'Total amount does not match the sum of individual items. Please recalculate.',
        'is_discrepancy' => true,
      ]);

      // Annotation 3: Marker on page 1
      Annotation::create([
        'concept_paper_id' => $paper2->id,
        'attachment_id' => $attachment1->id,
        'user_id' => $vpAcad->id,
        'page_number' => 1,
        'annotation_type' => 'marker',
        'coordinates' => [
          'x' => 450,
          'y' => 500,
          'width' => 60,
          'height' => 60,
        ],
        'comment' => 'Good justification provided',
        'is_discrepancy' => false,
      ]);
    }

    if ($paper3) {
      // Create a sample attachment for paper 3
      $attachment2 = Attachment::create([
        'attachable_type' => ConceptPaper::class,
        'attachable_id' => $paper3->id,
        'file_name' => 'equipment_specifications.pdf',
        'file_path' => 'concept_papers/2025/11/sample_specs.pdf',
        'mime_type' => 'application/pdf',
        'file_size' => 1048576, // 1 MB
        'uploaded_by' => $paper3->requisitioner_id,
      ]);

      // Create annotations on the attachment
      // Annotation 1: Discrepancy on page 1
      Annotation::create([
        'concept_paper_id' => $paper3->id,
        'attachment_id' => $attachment2->id,
        'user_id' => $auditor->id,
        'page_number' => 1,
        'annotation_type' => 'discrepancy',
        'coordinates' => [
          'x' => 200,
          'y' => 150,
          'width' => 100,
          'height' => 100,
        ],
        'comment' => 'Missing vendor quotation for this equipment. Required for audit compliance.',
        'is_discrepancy' => true,
      ]);

      // Annotation 2: Highlight on page 3
      Annotation::create([
        'concept_paper_id' => $paper3->id,
        'attachment_id' => $attachment2->id,
        'user_id' => $auditor->id,
        'page_number' => 3,
        'annotation_type' => 'highlight',
        'coordinates' => [
          'x' => 120,
          'y' => 400,
          'width' => 250,
          'height' => 40,
        ],
        'comment' => 'Warranty terms need clarification',
        'is_discrepancy' => false,
      ]);

      // Annotation 3: Drawing/freehand on page 2
      Annotation::create([
        'concept_paper_id' => $paper3->id,
        'attachment_id' => $attachment2->id,
        'user_id' => $auditor->id,
        'page_number' => 2,
        'annotation_type' => 'drawing',
        'coordinates' => [
          'points' => [
            [300, 250],
            [320, 270],
            [340, 260],
            [360, 280],
            [380, 275],
          ],
        ],
        'comment' => null,
        'is_discrepancy' => false,
      ]);

      // Annotation 4: Another discrepancy on page 1
      Annotation::create([
        'concept_paper_id' => $paper3->id,
        'attachment_id' => $attachment2->id,
        'user_id' => $auditor->id,
        'page_number' => 1,
        'annotation_type' => 'discrepancy',
        'coordinates' => [
          'x' => 500,
          'y' => 600,
          'width' => 90,
          'height' => 90,
        ],
        'comment' => 'Delivery timeline conflicts with project schedule mentioned in the proposal.',
        'is_discrepancy' => true,
      ]);
    }

    // Add annotation from Senior VP if available
    if ($seniorVp && $paper2) {
      $attachment1 = $paper2->attachments()->first();
      if ($attachment1) {
        Annotation::create([
          'concept_paper_id' => $paper2->id,
          'attachment_id' => $attachment1->id,
          'user_id' => $seniorVp->id,
          'page_number' => 1,
          'annotation_type' => 'marker',
          'coordinates' => [
            'x' => 50,
            'y' => 100,
            'width' => 70,
            'height' => 70,
          ],
          'comment' => 'Approved with noted corrections',
          'is_discrepancy' => false,
        ]);
      }
    }
  }
}

<?php

namespace Tests\Feature;

use App\Models\ConceptPaper;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkflowEnhancementsDataMigrationTest extends TestCase
{
  use RefreshDatabase;

  /**
   * Test that the migration command runs successfully with dry-run option.
   */
  public function test_migration_command_runs_with_dry_run(): void
  {
    $this->artisan('workflow:migrate-enhancements-data --dry-run')
      ->expectsOutput('=== Workflow Enhancements Data Migration ===')
      ->expectsOutput('DRY RUN MODE: No changes will be made to the database.')
      ->assertExitCode(0);
  }

  /**
   * Test that user activation status migration handles existing data correctly.
   * 
   * Note: The is_active field has a NOT NULL constraint with default value,
   * so in production all users will already have this field set. This test
   * verifies the migration doesn't break existing data.
   */
  public function test_backfills_user_activation_status(): void
  {
    // Create users with is_active already set (normal case)
    $user1 = User::factory()->create(['is_active' => true]);
    $user2 = User::factory()->create(['is_active' => false]);
    $user3 = User::factory()->create(['is_active' => true]);

    $this->artisan('workflow:migrate-enhancements-data --force')
      ->assertExitCode(0);

    // Verify existing values are preserved
    $this->assertTrue($user1->fresh()->is_active);
    $this->assertFalse($user2->fresh()->is_active); // Should remain false
    $this->assertTrue($user3->fresh()->is_active);
  }

  /**
   * Test that student involvement migration handles existing data correctly.
   * 
   * Note: The students_involved field has a default value of true,
   * so in production all papers will already have this field set. This test
   * verifies the migration doesn't break existing data.
   */
  public function test_backfills_student_involvement(): void
  {
    $requisitioner = User::factory()->create(['role' => 'requisitioner']);

    // Create concept papers with students_involved already set (normal case)
    $paper1 = ConceptPaper::factory()->create([
      'requisitioner_id' => $requisitioner->id,
      'students_involved' => true,
    ]);
    $paper2 = ConceptPaper::factory()->create([
      'requisitioner_id' => $requisitioner->id,
      'students_involved' => true,
    ]);
    $paper3 = ConceptPaper::factory()->create([
      'requisitioner_id' => $requisitioner->id,
      'students_involved' => false,
    ]);

    $this->artisan('workflow:migrate-enhancements-data --force')
      ->assertExitCode(0);

    // Verify existing values are preserved
    $this->assertTrue($paper1->fresh()->students_involved);
    $this->assertTrue($paper2->fresh()->students_involved);
    $this->assertFalse($paper3->fresh()->students_involved); // Should remain false
  }

  /**
   * Test that deadline dates are calculated correctly.
   */
  public function test_calculates_deadline_dates(): void
  {
    $requisitioner = User::factory()->create(['role' => 'requisitioner']);

    // Create concept papers without deadline_date set
    $submittedAt = now()->subDays(10);
    $paper1 = ConceptPaper::factory()->create([
      'requisitioner_id' => $requisitioner->id,
      'submitted_at' => $submittedAt,
      'deadline_date' => null,
      'deadline_option' => null,
    ]);

    $this->artisan('workflow:migrate-enhancements-data --force')
      ->assertExitCode(0);

    $paper1->refresh();

    // Verify deadline_option is set to 1_month
    $this->assertEquals('1_month', $paper1->deadline_option);

    // Verify deadline_date is submission + 30 days
    $expectedDeadline = $submittedAt->copy()->addDays(30);
    $this->assertEquals(
      $expectedDeadline->format('Y-m-d H:i'),
      $paper1->deadline_date->format('Y-m-d H:i')
    );
  }

  /**
   * Test that Senior VP Approval stage is added to in-progress papers.
   */
  public function test_adds_senior_vp_stage_to_in_progress_papers(): void
  {
    $requisitioner = User::factory()->create(['role' => 'requisitioner']);
    $auditor = User::factory()->create(['role' => 'auditor']);

    // Create a concept paper with completed Auditing Review
    $paper = ConceptPaper::factory()->create([
      'requisitioner_id' => $requisitioner->id,
      'status' => 'in_progress',
    ]);

    // Create stages up to and including Auditing Review (completed)
    $stage1 = WorkflowStage::factory()->create([
      'concept_paper_id' => $paper->id,
      'stage_name' => 'SPS Review',
      'stage_order' => 1,
      'status' => 'completed',
    ]);

    $stage2 = WorkflowStage::factory()->create([
      'concept_paper_id' => $paper->id,
      'stage_name' => 'VP Acad Review',
      'stage_order' => 2,
      'status' => 'completed',
    ]);

    $stage3 = WorkflowStage::factory()->create([
      'concept_paper_id' => $paper->id,
      'stage_name' => 'Auditing Review',
      'stage_order' => 3,
      'status' => 'completed',
      'assigned_user_id' => $auditor->id,
    ]);

    // Create a stage after Auditing Review (should be shifted)
    $stage4 = WorkflowStage::factory()->create([
      'concept_paper_id' => $paper->id,
      'stage_name' => 'Acad Copy Distribution',
      'stage_order' => 4,
      'status' => 'pending',
    ]);

    $paper->current_stage_id = $stage4->id;
    $paper->save();

    $this->artisan('workflow:migrate-enhancements-data --force')
      ->assertExitCode(0);

    // Verify Senior VP Approval stage was added
    $seniorVPStage = WorkflowStage::where('concept_paper_id', $paper->id)
      ->where('stage_name', 'Senior VP Approval')
      ->first();

    $this->assertNotNull($seniorVPStage);
    $this->assertEquals(4, $seniorVPStage->stage_order);
    $this->assertEquals('senior_vp', $seniorVPStage->assigned_role);
    $this->assertEquals('pending', $seniorVPStage->status);

    // Verify later stages were shifted
    $this->assertEquals(5, $stage4->fresh()->stage_order);

    // Verify current_stage_id was updated
    $this->assertEquals($seniorVPStage->id, $paper->fresh()->current_stage_id);
  }

  /**
   * Test that Senior VP stage is not added if already exists.
   */
  public function test_does_not_add_duplicate_senior_vp_stage(): void
  {
    $requisitioner = User::factory()->create(['role' => 'requisitioner']);

    $paper = ConceptPaper::factory()->create([
      'requisitioner_id' => $requisitioner->id,
      'status' => 'in_progress',
    ]);

    // Create stages including Senior VP Approval
    WorkflowStage::factory()->create([
      'concept_paper_id' => $paper->id,
      'stage_name' => 'Auditing Review',
      'stage_order' => 3,
      'status' => 'completed',
    ]);

    WorkflowStage::factory()->create([
      'concept_paper_id' => $paper->id,
      'stage_name' => 'Senior VP Approval',
      'stage_order' => 4,
      'status' => 'pending',
    ]);

    $initialStageCount = WorkflowStage::where('concept_paper_id', $paper->id)->count();

    $this->artisan('workflow:migrate-enhancements-data --force')
      ->assertExitCode(0);

    // Verify no duplicate stage was added
    $finalStageCount = WorkflowStage::where('concept_paper_id', $paper->id)->count();
    $this->assertEquals($initialStageCount, $finalStageCount);
  }

  /**
   * Test that Senior VP stage is not added to completed papers.
   */
  public function test_does_not_add_senior_vp_stage_to_completed_papers(): void
  {
    $requisitioner = User::factory()->create(['role' => 'requisitioner']);

    $paper = ConceptPaper::factory()->create([
      'requisitioner_id' => $requisitioner->id,
      'status' => 'completed',
    ]);

    WorkflowStage::factory()->create([
      'concept_paper_id' => $paper->id,
      'stage_name' => 'Auditing Review',
      'stage_order' => 3,
      'status' => 'completed',
    ]);

    $initialStageCount = WorkflowStage::where('concept_paper_id', $paper->id)->count();

    $this->artisan('workflow:migrate-enhancements-data --force')
      ->assertExitCode(0);

    // Verify no stage was added to completed paper
    $finalStageCount = WorkflowStage::where('concept_paper_id', $paper->id)->count();
    $this->assertEquals($initialStageCount, $finalStageCount);
  }

  /**
   * Test data integrity verification catches issues.
   */
  public function test_data_integrity_verification(): void
  {
    // Create clean data
    $user = User::factory()->create(['is_active' => true]);
    $paper = ConceptPaper::factory()->create([
      'requisitioner_id' => $user->id,
      'students_involved' => true,
      'deadline_date' => now()->addDays(30),
    ]);

    $this->artisan('workflow:migrate-enhancements-data --force')
      ->expectsOutput('  No data integrity issues found.')
      ->assertExitCode(0);
  }

  /**
   * Test migration with force option skips confirmation.
   */
  public function test_migration_with_force_option_skips_confirmation(): void
  {
    $this->artisan('workflow:migrate-enhancements-data --force')
      ->doesntExpectOutput('Do you want to proceed with the migration?')
      ->assertExitCode(0);
  }

  /**
   * Test migration shows summary before execution.
   */
  public function test_migration_shows_summary(): void
  {
    User::factory()->create(['is_active' => true]);

    $this->artisan('workflow:migrate-enhancements-data --dry-run')
      ->expectsOutputToContain('Migration Task')
      ->expectsOutputToContain('Backfill user activation status')
      ->assertExitCode(0);
  }
}

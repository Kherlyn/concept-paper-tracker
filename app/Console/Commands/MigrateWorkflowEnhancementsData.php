<?php

namespace App\Console\Commands;

use App\Models\ConceptPaper;
use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateWorkflowEnhancementsData extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'workflow:migrate-enhancements-data 
                            {--dry-run : Run the migration without making changes}
                            {--force : Skip confirmation prompt}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Migrate existing data for workflow enhancements (user activation, student involvement, deadlines, Senior VP stage)';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    $dryRun = $this->option('dry-run');
    $force = $this->option('force');

    $this->info('=== Workflow Enhancements Data Migration ===');
    $this->newLine();

    if ($dryRun) {
      $this->warn('DRY RUN MODE: No changes will be made to the database.');
      $this->newLine();
    }

    // Show what will be migrated
    $this->showMigrationSummary();

    if (!$force && !$dryRun) {
      if (!$this->confirm('Do you want to proceed with the migration?')) {
        $this->info('Migration cancelled.');
        return 0;
      }
    }

    $this->newLine();

    try {
      DB::beginTransaction();

      // Step 1: Backfill user activation status
      $this->info('Step 1: Backfilling user activation status...');
      $usersUpdated = $this->backfillUserActivation($dryRun);
      $this->info("✓ {$usersUpdated} users updated with is_active = true");
      $this->newLine();

      // Step 2: Backfill student involvement
      $this->info('Step 2: Backfilling student involvement...');
      $papersUpdated = $this->backfillStudentInvolvement($dryRun);
      $this->info("✓ {$papersUpdated} concept papers updated with students_involved = true");
      $this->newLine();

      // Step 3: Calculate and set deadline dates
      $this->info('Step 3: Calculating and setting deadline dates...');
      $deadlinesSet = $this->backfillDeadlineDates($dryRun);
      $this->info("✓ {$deadlinesSet} concept papers updated with deadline dates");
      $this->newLine();

      // Step 4: Update workflow stages for in-progress papers
      $this->info('Step 4: Updating workflow stages for in-progress papers...');
      $stagesAdded = $this->addSeniorVPStage($dryRun);
      $this->info("✓ {$stagesAdded} Senior VP Approval stages added");
      $this->newLine();

      // Step 5: Verify data integrity
      $this->info('Step 5: Verifying data integrity...');
      $this->verifyDataIntegrity();
      $this->info('✓ Data integrity verification complete');
      $this->newLine();

      if (!$dryRun) {
        DB::commit();
        $this->info('✅ Migration completed successfully!');
      } else {
        DB::rollBack();
        $this->info('✅ Dry run completed successfully! No changes were made.');
      }

      return 0;
    } catch (\Exception $e) {
      DB::rollBack();
      $this->error('❌ Migration failed: ' . $e->getMessage());
      $this->error($e->getTraceAsString());
      return 1;
    }
  }

  /**
   * Show a summary of what will be migrated.
   */
  protected function showMigrationSummary()
  {
    $usersWithoutActivation = User::whereNull('is_active')->count();
    $papersWithoutStudentFlag = ConceptPaper::whereNull('students_involved')->count();
    $papersWithoutDeadline = ConceptPaper::whereNull('deadline_date')->count();
    $inProgressPapers = ConceptPaper::whereIn('status', ['pending', 'in_progress'])
      ->whereHas('stages', function ($query) {
        $query->where('stage_name', 'Auditing Review')
          ->where('status', 'completed');
      })
      ->whereDoesntHave('stages', function ($query) {
        $query->where('stage_name', 'Senior VP Approval');
      })
      ->count();

    $this->table(
      ['Migration Task', 'Records Affected'],
      [
        ['Backfill user activation status', $usersWithoutActivation],
        ['Backfill student involvement', $papersWithoutStudentFlag],
        ['Calculate deadline dates', $papersWithoutDeadline],
        ['Add Senior VP Approval stage', $inProgressPapers],
      ]
    );
    $this->newLine();
  }

  /**
   * Backfill is_active = true for existing users.
   */
  protected function backfillUserActivation(bool $dryRun): int
  {
    $users = User::whereNull('is_active')->get();

    if ($dryRun) {
      return $users->count();
    }

    $count = 0;
    foreach ($users as $user) {
      $user->is_active = true;
      $user->save();
      $count++;
    }

    return $count;
  }

  /**
   * Backfill students_involved = true for existing papers.
   */
  protected function backfillStudentInvolvement(bool $dryRun): int
  {
    $papers = ConceptPaper::whereNull('students_involved')->get();

    if ($dryRun) {
      return $papers->count();
    }

    $count = 0;
    foreach ($papers as $paper) {
      $paper->students_involved = true;
      $paper->save();
      $count++;
    }

    return $count;
  }

  /**
   * Calculate and set deadline_date for existing papers (submission + 1 month).
   */
  protected function backfillDeadlineDates(bool $dryRun): int
  {
    $papers = ConceptPaper::whereNull('deadline_date')->get();

    if ($dryRun) {
      return $papers->count();
    }

    $count = 0;
    foreach ($papers as $paper) {
      // Set deadline to 1 month (30 days) after submission
      $paper->deadline_option = '1_month';
      $paper->deadline_date = $paper->submitted_at->addDays(30);
      $paper->save();
      $count++;
    }

    return $count;
  }

  /**
   * Add Senior VP Approval stage for in-progress papers that have completed Auditing Review.
   */
  protected function addSeniorVPStage(bool $dryRun): int
  {
    // Find papers that have completed Auditing Review but don't have Senior VP Approval stage
    $papers = ConceptPaper::whereIn('status', ['pending', 'in_progress'])
      ->whereHas('stages', function ($query) {
        $query->where('stage_name', 'Auditing Review')
          ->where('status', 'completed');
      })
      ->whereDoesntHave('stages', function ($query) {
        $query->where('stage_name', 'Senior VP Approval');
      })
      ->get();

    if ($dryRun) {
      return $papers->count();
    }

    $count = 0;
    foreach ($papers as $paper) {
      // Get the Auditing Review stage
      $auditingStage = $paper->stages()
        ->where('stage_name', 'Auditing Review')
        ->first();

      if (!$auditingStage) {
        continue;
      }

      // Get all stages after Auditing Review
      $laterStages = $paper->stages()
        ->where('stage_order', '>', $auditingStage->stage_order)
        ->orderBy('stage_order')
        ->get();

      // Increment stage_order for all later stages
      foreach ($laterStages as $stage) {
        $stage->stage_order = $stage->stage_order + 1;
        $stage->save();
      }

      // Create the Senior VP Approval stage
      $seniorVPStage = WorkflowStage::create([
        'concept_paper_id' => $paper->id,
        'stage_name' => 'Senior VP Approval',
        'stage_order' => $auditingStage->stage_order + 1,
        'assigned_role' => 'senior_vp',
        'assigned_user_id' => null, // Will be assigned by admin
        'status' => 'pending',
        'deadline' => now()->addDays(2), // 2 days as per config
      ]);

      // If the current stage is after Auditing Review, update current_stage_id
      if ($paper->current_stage_id && $paper->currentStage->stage_order > $auditingStage->stage_order) {
        // The current stage should now be Senior VP Approval
        $paper->current_stage_id = $seniorVPStage->id;
        $paper->save();
      }

      $count++;
    }

    return $count;
  }

  /**
   * Verify data integrity after migration.
   */
  protected function verifyDataIntegrity()
  {
    $issues = [];

    // Check 1: All users should have is_active set
    $usersWithoutActivation = User::whereNull('is_active')->count();
    if ($usersWithoutActivation > 0) {
      $issues[] = "{$usersWithoutActivation} users still have NULL is_active";
    }

    // Check 2: All concept papers should have students_involved set
    $papersWithoutStudentFlag = ConceptPaper::whereNull('students_involved')->count();
    if ($papersWithoutStudentFlag > 0) {
      $issues[] = "{$papersWithoutStudentFlag} concept papers still have NULL students_involved";
    }

    // Check 3: All concept papers should have deadline_date set
    $papersWithoutDeadline = ConceptPaper::whereNull('deadline_date')->count();
    if ($papersWithoutDeadline > 0) {
      $issues[] = "{$papersWithoutDeadline} concept papers still have NULL deadline_date";
    }

    // Check 4: Verify stage ordering is correct
    $papersWithDuplicateOrders = DB::table('workflow_stages')
      ->select('concept_paper_id', 'stage_order', DB::raw('COUNT(*) as count'))
      ->groupBy('concept_paper_id', 'stage_order')
      ->having('count', '>', 1)
      ->count();

    if ($papersWithDuplicateOrders > 0) {
      $issues[] = "{$papersWithDuplicateOrders} concept papers have duplicate stage orders";
    }

    if (count($issues) > 0) {
      $this->warn('⚠️  Data integrity issues found:');
      foreach ($issues as $issue) {
        $this->warn('  - ' . $issue);
      }
    } else {
      $this->info('  No data integrity issues found.');
    }
  }
}

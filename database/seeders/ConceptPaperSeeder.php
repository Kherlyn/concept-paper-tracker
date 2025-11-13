<?php

namespace Database\Seeders;

use App\Models\ConceptPaper;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\WorkflowService;
use Illuminate\Database\Seeder;

class ConceptPaperSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $workflowService = app(WorkflowService::class);

    // Get users for different roles
    $requisitioner = User::where('role', 'requisitioner')->first();
    $sps = User::where('role', 'sps')->first();
    $vpAcad = User::where('role', 'vp_acad')->first();
    $auditor = User::where('role', 'auditor')->first();
    $accounting = User::where('role', 'accounting')->first();

    // Paper 1: Completed paper
    $paper1 = new ConceptPaper();
    $paper1->requisitioner_id = $requisitioner->id;
    $paper1->department = 'Computer Science';
    $paper1->title = 'Laboratory Equipment Upgrade';
    $paper1->nature_of_request = 'regular';
    $paper1->submitted_at = now()->subDays(20);
    $paper1->save();

    $workflowService->initializeWorkflow($paper1);

    // Complete all stages for paper 1
    foreach ($paper1->stages as $stage) {
      $stage->update([
        'status' => 'completed',
        'started_at' => now()->subDays(20 - ($stage->stage_order * 2)),
        'completed_at' => now()->subDays(20 - ($stage->stage_order * 2) - 1),
      ]);

      // Create audit log for completion
      AuditLog::create([
        'concept_paper_id' => $paper1->id,
        'user_id' => $this->getUserForRole($stage->assigned_role)->id,
        'action' => 'completed',
        'stage_name' => $stage->stage_name,
        'remarks' => 'Stage completed successfully',
      ]);
    }

    $paper1->update([
      'current_stage_id' => null,
      'status' => 'completed',
      'completed_at' => now()->subDays(5),
    ]);

    // Create submission audit log
    AuditLog::create([
      'concept_paper_id' => $paper1->id,
      'user_id' => $requisitioner->id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Concept paper submitted',
    ]);

    // Paper 2: In progress at VP Acad Review (stage 2)
    $paper2 = new ConceptPaper();
    $paper2->requisitioner_id = $requisitioner->id;
    $paper2->department = 'Computer Science';
    $paper2->title = 'Student Research Grant Program';
    $paper2->nature_of_request = 'urgent';
    $paper2->submitted_at = now()->subDays(5);
    $paper2->save();

    $workflowService->initializeWorkflow($paper2);

    // Complete stage 1
    $stage1 = $paper2->stages()->where('stage_order', 1)->first();
    $stage1->update([
      'status' => 'completed',
      'started_at' => now()->subDays(5),
      'completed_at' => now()->subDays(4),
    ]);

    AuditLog::create([
      'concept_paper_id' => $paper2->id,
      'user_id' => $sps->id,
      'action' => 'completed',
      'stage_name' => $stage1->stage_name,
      'remarks' => 'Approved by SPS',
    ]);

    // Set stage 2 as current and in progress
    $stage2 = $paper2->stages()->where('stage_order', 2)->first();
    $stage2->update([
      'status' => 'in_progress',
      'started_at' => now()->subDays(4),
    ]);

    $paper2->update(['current_stage_id' => $stage2->id]);

    AuditLog::create([
      'concept_paper_id' => $paper2->id,
      'user_id' => $requisitioner->id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Concept paper submitted',
    ]);

    // Paper 3: Overdue at Auditing Review (stage 3)
    $paper3 = new ConceptPaper();
    $paper3->requisitioner_id = $requisitioner->id;
    $paper3->department = 'Engineering';
    $paper3->title = 'Workshop Equipment Purchase';
    $paper3->nature_of_request = 'regular';
    $paper3->submitted_at = now()->subDays(10);
    $paper3->save();

    $workflowService->initializeWorkflow($paper3);

    // Complete stages 1 and 2
    $stage1 = $paper3->stages()->where('stage_order', 1)->first();
    $stage1->update([
      'status' => 'completed',
      'started_at' => now()->subDays(10),
      'completed_at' => now()->subDays(9),
    ]);

    AuditLog::create([
      'concept_paper_id' => $paper3->id,
      'user_id' => $sps->id,
      'action' => 'completed',
      'stage_name' => $stage1->stage_name,
      'remarks' => 'Approved',
    ]);

    $stage2 = $paper3->stages()->where('stage_order', 2)->first();
    $stage2->update([
      'status' => 'completed',
      'started_at' => now()->subDays(9),
      'completed_at' => now()->subDays(7),
    ]);

    AuditLog::create([
      'concept_paper_id' => $paper3->id,
      'user_id' => $vpAcad->id,
      'action' => 'completed',
      'stage_name' => $stage2->stage_name,
      'remarks' => 'Approved by VP Acad',
    ]);

    // Set stage 3 as current and overdue
    $stage3 = $paper3->stages()->where('stage_order', 3)->first();
    $stage3->update([
      'status' => 'in_progress',
      'started_at' => now()->subDays(7),
      'deadline' => now()->subDays(2), // Make it overdue
    ]);

    $paper3->update(['current_stage_id' => $stage3->id]);

    AuditLog::create([
      'concept_paper_id' => $paper3->id,
      'user_id' => $requisitioner->id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Concept paper submitted',
    ]);

    // Paper 4: Just submitted (pending at stage 1)
    $paper4 = new ConceptPaper();
    $paper4->requisitioner_id = $requisitioner->id;
    $paper4->department = 'Business Administration';
    $paper4->title = 'Faculty Development Training';
    $paper4->nature_of_request = 'regular';
    $paper4->submitted_at = now()->subHours(2);
    $paper4->save();

    $workflowService->initializeWorkflow($paper4);

    AuditLog::create([
      'concept_paper_id' => $paper4->id,
      'user_id' => $requisitioner->id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Concept paper submitted',
    ]);

    // Paper 5: Returned from VP Acad to SPS
    $paper5 = new ConceptPaper();
    $paper5->requisitioner_id = $requisitioner->id;
    $paper5->department = 'Arts and Sciences';
    $paper5->title = 'Library Book Acquisition';
    $paper5->nature_of_request = 'urgent';
    $paper5->submitted_at = now()->subDays(6);
    $paper5->save();

    $workflowService->initializeWorkflow($paper5);

    // Complete stage 1
    $stage1 = $paper5->stages()->where('stage_order', 1)->first();
    $stage1->update([
      'status' => 'completed',
      'started_at' => now()->subDays(6),
      'completed_at' => now()->subDays(5),
    ]);

    AuditLog::create([
      'concept_paper_id' => $paper5->id,
      'user_id' => $sps->id,
      'action' => 'completed',
      'stage_name' => $stage1->stage_name,
      'remarks' => 'Approved by SPS',
    ]);

    // Stage 2 was started but returned
    $stage2 = $paper5->stages()->where('stage_order', 2)->first();
    $stage2->update([
      'status' => 'returned',
      'started_at' => now()->subDays(5),
      'remarks' => 'Please provide more detailed budget breakdown',
    ]);

    AuditLog::create([
      'concept_paper_id' => $paper5->id,
      'user_id' => $vpAcad->id,
      'action' => 'returned',
      'stage_name' => $stage2->stage_name,
      'remarks' => 'Please provide more detailed budget breakdown',
    ]);

    // Return to stage 1
    $stage1->update([
      'status' => 'in_progress',
      'started_at' => now()->subDays(4),
      'completed_at' => null,
    ]);

    $paper5->update(['current_stage_id' => $stage1->id]);

    AuditLog::create([
      'concept_paper_id' => $paper5->id,
      'user_id' => $requisitioner->id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Concept paper submitted',
    ]);

    // Paper 6: Emergency request at Voucher Preparation (stage 6)
    $paper6 = new ConceptPaper();
    $paper6->requisitioner_id = $requisitioner->id;
    $paper6->department = 'Computer Science';
    $paper6->title = 'Emergency Server Repair';
    $paper6->nature_of_request = 'emergency';
    $paper6->submitted_at = now()->subDays(8);
    $paper6->save();

    $workflowService->initializeWorkflow($paper6);

    // Complete stages 1-5
    for ($i = 1; $i <= 5; $i++) {
      $stage = $paper6->stages()->where('stage_order', $i)->first();
      $stage->update([
        'status' => 'completed',
        'started_at' => now()->subDays(8 - $i),
        'completed_at' => now()->subDays(8 - $i - 0.5),
      ]);

      AuditLog::create([
        'concept_paper_id' => $paper6->id,
        'user_id' => $this->getUserForRole($stage->assigned_role)->id,
        'action' => 'completed',
        'stage_name' => $stage->stage_name,
        'remarks' => 'Expedited approval for emergency',
      ]);
    }

    // Set stage 6 as current
    $stage6 = $paper6->stages()->where('stage_order', 6)->first();
    $stage6->update([
      'status' => 'in_progress',
      'started_at' => now()->subDays(3),
    ]);

    $paper6->update(['current_stage_id' => $stage6->id]);

    AuditLog::create([
      'concept_paper_id' => $paper6->id,
      'user_id' => $requisitioner->id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Emergency concept paper submitted',
    ]);

    // Paper 7: At Cheque Preparation (stage 8), slightly overdue
    $paper7 = new ConceptPaper();
    $paper7->requisitioner_id = $requisitioner->id;
    $paper7->department = 'Engineering';
    $paper7->title = 'Student Scholarship Fund';
    $paper7->nature_of_request = 'regular';
    $paper7->submitted_at = now()->subDays(18);
    $paper7->save();

    $workflowService->initializeWorkflow($paper7);

    // Complete stages 1-7
    for ($i = 1; $i <= 7; $i++) {
      $stage = $paper7->stages()->where('stage_order', $i)->first();
      $stage->update([
        'status' => 'completed',
        'started_at' => now()->subDays(18 - ($i * 2)),
        'completed_at' => now()->subDays(18 - ($i * 2) - 1),
      ]);

      AuditLog::create([
        'concept_paper_id' => $paper7->id,
        'user_id' => $this->getUserForRole($stage->assigned_role)->id,
        'action' => 'completed',
        'stage_name' => $stage->stage_name,
        'remarks' => 'Approved',
      ]);
    }

    // Set stage 8 as current and overdue
    $stage8 = $paper7->stages()->where('stage_order', 8)->first();
    $stage8->update([
      'status' => 'in_progress',
      'started_at' => now()->subDays(6),
      'deadline' => now()->subDays(1), // Make it overdue
    ]);

    $paper7->update(['current_stage_id' => $stage8->id]);

    AuditLog::create([
      'concept_paper_id' => $paper7->id,
      'user_id' => $requisitioner->id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Concept paper submitted',
    ]);

    // Paper 8: Another completed paper from last month
    $paper8 = new ConceptPaper();
    $paper8->requisitioner_id = $requisitioner->id;
    $paper8->department = 'Business Administration';
    $paper8->title = 'Marketing Materials Production';
    $paper8->nature_of_request = 'regular';
    $paper8->submitted_at = now()->subDays(35);
    $paper8->save();

    $workflowService->initializeWorkflow($paper8);

    // Complete all stages
    foreach ($paper8->stages as $stage) {
      $stage->update([
        'status' => 'completed',
        'started_at' => now()->subDays(35 - ($stage->stage_order * 2)),
        'completed_at' => now()->subDays(35 - ($stage->stage_order * 2) - 1),
      ]);

      AuditLog::create([
        'concept_paper_id' => $paper8->id,
        'user_id' => $this->getUserForRole($stage->assigned_role)->id,
        'action' => 'completed',
        'stage_name' => $stage->stage_name,
        'remarks' => 'Approved',
      ]);
    }

    $paper8->update([
      'current_stage_id' => null,
      'status' => 'completed',
      'completed_at' => now()->subDays(20),
    ]);

    AuditLog::create([
      'concept_paper_id' => $paper8->id,
      'user_id' => $requisitioner->id,
      'action' => 'submitted',
      'stage_name' => null,
      'remarks' => 'Concept paper submitted',
    ]);
  }

  /**
   * Get a user for a specific role.
   *
   * @param string $role
   * @return User
   */
  private function getUserForRole(string $role): User
  {
    return User::where('role', $role)->first();
  }
}

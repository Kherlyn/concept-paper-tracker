<?php

namespace Tests\Feature;

use App\Models\Annotation;
use App\Models\ConceptPaper;
use App\Models\DeadlineOption;
use App\Models\User;
use Database\Seeders\AnnotationSeeder;
use Database\Seeders\ConceptPaperSeeder;
use Database\Seeders\DeadlineOptionSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
  use RefreshDatabase;

  public function test_deadline_option_seeder_creates_all_options(): void
  {
    $this->seed(DeadlineOptionSeeder::class);

    $this->assertDatabaseCount('deadline_options', 5);

    $this->assertDatabaseHas('deadline_options', [
      'key' => '1_week',
      'label' => '1 Week',
      'days' => 7,
    ]);

    $this->assertDatabaseHas('deadline_options', [
      'key' => '2_weeks',
      'label' => '2 Weeks',
      'days' => 14,
    ]);

    $this->assertDatabaseHas('deadline_options', [
      'key' => '1_month',
      'label' => '1 Month',
      'days' => 30,
    ]);

    $this->assertDatabaseHas('deadline_options', [
      'key' => '2_months',
      'label' => '2 Months',
      'days' => 60,
    ]);

    $this->assertDatabaseHas('deadline_options', [
      'key' => '3_months',
      'label' => '3 Months',
      'days' => 90,
    ]);
  }

  public function test_user_seeder_creates_senior_vp_users(): void
  {
    $this->seed(UserSeeder::class);

    $seniorVps = User::where('role', 'senior_vp')->get();

    $this->assertCount(2, $seniorVps);

    $this->assertDatabaseHas('users', [
      'email' => 'senior_vp@example.com',
      'role' => 'senior_vp',
      'is_active' => true,
    ]);

    $this->assertDatabaseHas('users', [
      'email' => 'senior_vp2@example.com',
      'role' => 'senior_vp',
      'is_active' => true,
    ]);
  }

  public function test_user_seeder_creates_inactive_user(): void
  {
    $this->seed(UserSeeder::class);

    $inactiveUsers = User::where('is_active', false)->get();

    $this->assertCount(1, $inactiveUsers);

    $this->assertDatabaseHas('users', [
      'email' => 'inactive@example.com',
      'is_active' => false,
    ]);

    $inactiveUser = User::where('email', 'inactive@example.com')->first();
    $this->assertNotNull($inactiveUser->deactivated_at);
    $this->assertNotNull($inactiveUser->deactivated_by);
  }

  public function test_concept_paper_seeder_includes_new_fields(): void
  {
    $this->seed([DeadlineOptionSeeder::class, UserSeeder::class, ConceptPaperSeeder::class]);

    $papers = ConceptPaper::all();

    $this->assertCount(8, $papers);

    // Check that all papers have deadline fields
    foreach ($papers as $paper) {
      $this->assertNotNull($paper->students_involved);
      $this->assertNotNull($paper->deadline_option);
      $this->assertNotNull($paper->deadline_date);
    }

    // Check specific papers
    $paper1 = ConceptPaper::where('title', 'Laboratory Equipment Upgrade')->first();
    $this->assertTrue($paper1->students_involved);
    $this->assertEquals('1_month', $paper1->deadline_option);

    $paper3 = ConceptPaper::where('title', 'Workshop Equipment Purchase')->first();
    $this->assertFalse($paper3->students_involved);
    $this->assertEquals('1_week', $paper3->deadline_option);

    $paper6 = ConceptPaper::where('title', 'Emergency Server Repair')->first();
    $this->assertFalse($paper6->students_involved);
    $this->assertEquals('1_week', $paper6->deadline_option);
  }

  public function test_annotation_seeder_creates_sample_annotations(): void
  {
    $this->seed([
      DeadlineOptionSeeder::class,
      UserSeeder::class,
      ConceptPaperSeeder::class,
      AnnotationSeeder::class,
    ]);

    $annotations = Annotation::all();

    $this->assertGreaterThanOrEqual(8, $annotations->count());

    // Check discrepancies
    $discrepancies = Annotation::where('is_discrepancy', true)->get();
    $this->assertGreaterThanOrEqual(3, $discrepancies->count());

    // Verify all discrepancies have comments
    foreach ($discrepancies as $discrepancy) {
      $this->assertNotNull($discrepancy->comment);
      $this->assertEquals('discrepancy', $discrepancy->annotation_type);
    }

    // Verify annotations have proper structure
    foreach ($annotations as $annotation) {
      $this->assertNotNull($annotation->concept_paper_id);
      $this->assertNotNull($annotation->attachment_id);
      $this->assertNotNull($annotation->user_id);
      $this->assertNotNull($annotation->page_number);
      $this->assertNotNull($annotation->annotation_type);
      $this->assertNotNull($annotation->coordinates);
      $this->assertIsArray($annotation->coordinates);
    }
  }

  public function test_annotation_seeder_creates_annotations_from_different_users(): void
  {
    $this->seed([
      DeadlineOptionSeeder::class,
      UserSeeder::class,
      ConceptPaperSeeder::class,
      AnnotationSeeder::class,
    ]);

    $vpAcad = User::where('role', 'vp_acad')->first();
    $auditor = User::where('role', 'auditor')->first();
    $seniorVp = User::where('role', 'senior_vp')->first();

    // Check VP Acad created annotations
    $vpAnnotations = Annotation::where('user_id', $vpAcad->id)->get();
    $this->assertGreaterThan(0, $vpAnnotations->count());

    // Check Auditor created annotations
    $auditorAnnotations = Annotation::where('user_id', $auditor->id)->get();
    $this->assertGreaterThan(0, $auditorAnnotations->count());

    // Check Senior VP created annotations
    $seniorVpAnnotations = Annotation::where('user_id', $seniorVp->id)->get();
    $this->assertGreaterThan(0, $seniorVpAnnotations->count());
  }

  public function test_seeders_maintain_backward_compatibility(): void
  {
    $this->seed([
      DeadlineOptionSeeder::class,
      UserSeeder::class,
      ConceptPaperSeeder::class,
      AnnotationSeeder::class,
    ]);

    // Verify all original user roles still exist
    $this->assertDatabaseHas('users', ['role' => 'requisitioner']);
    $this->assertDatabaseHas('users', ['role' => 'sps']);
    $this->assertDatabaseHas('users', ['role' => 'vp_acad']);
    $this->assertDatabaseHas('users', ['role' => 'auditor']);
    $this->assertDatabaseHas('users', ['role' => 'accounting']);
    $this->assertDatabaseHas('users', ['role' => 'admin']);

    // Verify all users are active by default (except the one inactive user)
    $activeUsers = User::where('is_active', true)->count();
    $this->assertEquals(11, $activeUsers);

    // Verify concept papers have proper workflow stages
    $papers = ConceptPaper::all();
    foreach ($papers as $paper) {
      $this->assertGreaterThan(0, $paper->stages()->count());
    }
  }
}

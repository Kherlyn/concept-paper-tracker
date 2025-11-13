<?php

namespace Database\Factories;

use App\Models\ConceptPaper;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkflowStage>
 */
class WorkflowStageFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'concept_paper_id' => ConceptPaper::factory(),
      'stage_name' => 'SPS Review',
      'stage_order' => 1,
      'assigned_role' => 'sps',
      'assigned_user_id' => null,
      'status' => 'pending',
      'started_at' => now(),
      'completed_at' => null,
      'deadline' => now()->addDay(),
      'remarks' => null,
    ];
  }

  /**
   * Indicate that the stage is completed.
   */
  public function completed(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'completed',
      'completed_at' => now(),
    ]);
  }

  /**
   * Indicate that the stage is in progress.
   */
  public function inProgress(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'in_progress',
    ]);
  }

  /**
   * Indicate that the stage is returned.
   */
  public function returned(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'returned',
      'remarks' => fake()->sentence(),
    ]);
  }
}

<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConceptPaper>
 */
class ConceptPaperFactory extends Factory
{
  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'tracking_number' => null, // Will be auto-generated
      'requisitioner_id' => User::factory(),
      'department' => fake()->randomElement(['Computer Science', 'Engineering', 'Business', 'Education']),
      'title' => fake()->sentence(),
      'nature_of_request' => fake()->randomElement(['regular', 'urgent', 'emergency']),
      'submitted_at' => now(),
      'current_stage_id' => null,
      'status' => 'pending',
      'completed_at' => null,
    ];
  }

  /**
   * Indicate that the concept paper is in progress.
   */
  public function inProgress(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'in_progress',
    ]);
  }

  /**
   * Indicate that the concept paper is completed.
   */
  public function completed(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'completed',
      'completed_at' => now(),
    ]);
  }

  /**
   * Indicate that the concept paper is returned.
   */
  public function returned(): static
  {
    return $this->state(fn(array $attributes) => [
      'status' => 'returned',
    ]);
  }
}

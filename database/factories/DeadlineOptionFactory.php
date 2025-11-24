<?php

namespace Database\Factories;

use App\Models\DeadlineOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeadlineOption>
 */
class DeadlineOptionFactory extends Factory
{
  protected $model = DeadlineOption::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'key' => $this->faker->unique()->slug(2),
      'label' => $this->faker->words(2, true),
      'days' => $this->faker->numberBetween(1, 365),
      'sort_order' => $this->faker->numberBetween(0, 100),
    ];
  }
}

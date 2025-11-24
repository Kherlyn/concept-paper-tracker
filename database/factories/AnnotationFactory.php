<?php

namespace Database\Factories;

use App\Models\Annotation;
use App\Models\Attachment;
use App\Models\ConceptPaper;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Annotation>
 */
class AnnotationFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Annotation::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    return [
      'concept_paper_id' => ConceptPaper::factory(),
      'attachment_id' => Attachment::factory(),
      'user_id' => User::factory(),
      'page_number' => $this->faker->numberBetween(1, 10),
      'annotation_type' => $this->faker->randomElement(['marker', 'highlight', 'drawing']),
      'coordinates' => [
        'x' => $this->faker->numberBetween(0, 1000),
        'y' => $this->faker->numberBetween(0, 1000),
        'width' => $this->faker->numberBetween(50, 200),
        'height' => $this->faker->numberBetween(50, 200),
      ],
      'comment' => $this->faker->optional()->sentence(),
      'is_discrepancy' => false,
    ];
  }

  /**
   * Indicate that the annotation is a discrepancy.
   *
   * @return static
   */
  public function discrepancy(): static
  {
    return $this->state(fn(array $attributes) => [
      'annotation_type' => 'discrepancy',
      'is_discrepancy' => true,
      'comment' => $this->faker->sentence(),
    ]);
  }

  /**
   * Indicate that the annotation has no comment.
   *
   * @return static
   */
  public function withoutComment(): static
  {
    return $this->state(fn(array $attributes) => [
      'comment' => null,
    ]);
  }
}

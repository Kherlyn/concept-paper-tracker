<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\ConceptPaper;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Attachment>
 */
class AttachmentFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Attachment::class;

  /**
   * Define the model's default state.
   *
   * @return array<string, mixed>
   */
  public function definition(): array
  {
    $fileName = $this->faker->word() . '.pdf';

    return [
      'attachable_type' => ConceptPaper::class,
      'attachable_id' => ConceptPaper::factory(),
      'file_name' => $fileName,
      'file_path' => 'concept_papers/2025/11/' . $fileName,
      'file_size' => $this->faker->numberBetween(100000, 5000000), // 100KB to 5MB
      'mime_type' => 'application/pdf',
      'uploaded_by' => User::factory(),
    ];
  }

  /**
   * Indicate that the attachment is a Word document.
   *
   * @return static
   */
  public function wordDocument(): static
  {
    return $this->state(function (array $attributes) {
      $fileName = $this->faker->word() . '.docx';
      return [
        'file_name' => $fileName,
        'file_path' => 'concept_papers/2025/11/' . $fileName,
        'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
      ];
    });
  }
}

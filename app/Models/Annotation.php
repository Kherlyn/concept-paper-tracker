<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Annotation extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<string>
   */
  protected $fillable = [
    'concept_paper_id',
    'attachment_id',
    'user_id',
    'page_number',
    'annotation_type',
    'coordinates',
    'comment',
    'is_discrepancy',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'coordinates' => 'array',
      'is_discrepancy' => 'boolean',
      'page_number' => 'integer',
    ];
  }

  /**
   * Relationship: The concept paper this annotation belongs to.
   *
   * @return BelongsTo
   */
  public function conceptPaper(): BelongsTo
  {
    return $this->belongsTo(ConceptPaper::class, 'concept_paper_id');
  }

  /**
   * Relationship: The attachment this annotation is on.
   *
   * @return BelongsTo
   */
  public function attachment(): BelongsTo
  {
    return $this->belongsTo(Attachment::class, 'attachment_id');
  }

  /**
   * Relationship: The user who created this annotation.
   *
   * @return BelongsTo
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'user_id');
  }

  /**
   * Validate that coordinates are within document bounds.
   *
   * @param array $coordinates
   * @param int $maxWidth
   * @param int $maxHeight
   * @return bool
   */
  public static function validateCoordinates(array $coordinates, int $maxWidth, int $maxHeight): bool
  {
    // Check if required coordinate fields exist
    if (!isset($coordinates['x']) || !isset($coordinates['y'])) {
      return false;
    }

    // Validate x and y are within bounds
    if ($coordinates['x'] < 0 || $coordinates['x'] > $maxWidth) {
      return false;
    }

    if ($coordinates['y'] < 0 || $coordinates['y'] > $maxHeight) {
      return false;
    }

    // If width and height are provided, validate them
    if (isset($coordinates['width'])) {
      if ($coordinates['width'] < 0 || ($coordinates['x'] + $coordinates['width']) > $maxWidth) {
        return false;
      }
    }

    if (isset($coordinates['height'])) {
      if ($coordinates['height'] < 0 || ($coordinates['y'] + $coordinates['height']) > $maxHeight) {
        return false;
      }
    }

    // If points array is provided (for freehand), validate each point
    if (isset($coordinates['points']) && is_array($coordinates['points'])) {
      foreach ($coordinates['points'] as $point) {
        if (!is_array($point) || count($point) !== 2) {
          return false;
        }

        [$x, $y] = $point;
        if ($x < 0 || $x > $maxWidth || $y < 0 || $y > $maxHeight) {
          return false;
        }
      }
    }

    return true;
  }

  /**
   * Get validation rules for annotation creation.
   *
   * @return array<string, mixed>
   */
  public static function validationRules(): array
  {
    return [
      'concept_paper_id' => 'required|exists:concept_papers,id',
      'attachment_id' => 'required|exists:attachments,id',
      'user_id' => 'required|exists:users,id',
      'page_number' => 'required|integer|min:1',
      'annotation_type' => 'required|in:marker,highlight,discrepancy,drawing',
      'coordinates' => 'required|array',
      'coordinates.x' => 'required|numeric|min:0',
      'coordinates.y' => 'required|numeric|min:0',
      'coordinates.width' => 'nullable|numeric|min:0',
      'coordinates.height' => 'nullable|numeric|min:0',
      'coordinates.points' => 'nullable|array',
      'coordinates.points.*' => 'array|size:2',
      'comment' => 'nullable|string|max:5000',
      'is_discrepancy' => 'boolean',
    ];
  }

  /**
   * Get validation rules for discrepancy annotations.
   * Discrepancies require a comment.
   *
   * @return array<string, mixed>
   */
  public static function discrepancyValidationRules(): array
  {
    $rules = self::validationRules();
    $rules['comment'] = 'required|string|max:5000';
    return $rules;
  }
}

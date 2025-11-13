<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'concept_paper_id',
    'user_id',
    'action',
    'stage_name',
    'remarks',
    'metadata',
  ];

  /**
   * The attributes that aren't mass assignable.
   * Timestamps are guarded to prevent manual modification.
   *
   * @var list<string>
   */
  protected $guarded = [
    'created_at',
  ];

  /**
   * Indicates if the model should be timestamped.
   * We only use created_at, not updated_at.
   *
   * @var bool
   */
  public const UPDATED_AT = null;

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'metadata' => 'array',
      'created_at' => 'immutable_datetime',
    ];
  }

  /**
   * Relationship: The concept paper this audit log belongs to.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function conceptPaper()
  {
    return $this->belongsTo(ConceptPaper::class, 'concept_paper_id');
  }

  /**
   * Relationship: The user who performed the action.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function user()
  {
    return $this->belongsTo(User::class, 'user_id');
  }
}

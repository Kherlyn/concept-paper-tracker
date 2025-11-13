<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class WorkflowStage extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<string>
   */
  protected $fillable = [
    'concept_paper_id',
    'stage_name',
    'stage_order',
    'assigned_role',
    'assigned_user_id',
    'status',
    'started_at',
    'completed_at',
    'deadline',
    'remarks',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'stage_order' => 'integer',
      'started_at' => 'datetime',
      'completed_at' => 'datetime',
      'deadline' => 'datetime',
    ];
  }

  /**
   * Relationship: The concept paper this stage belongs to.
   *
   * @return BelongsTo
   */
  public function conceptPaper(): BelongsTo
  {
    return $this->belongsTo(ConceptPaper::class, 'concept_paper_id');
  }

  /**
   * Relationship: The user assigned to this stage.
   *
   * @return BelongsTo
   */
  public function assignedUser(): BelongsTo
  {
    return $this->belongsTo(User::class, 'assigned_user_id');
  }

  /**
   * Relationship: All attachments for this workflow stage.
   *
   * @return MorphMany
   */
  public function attachments(): MorphMany
  {
    return $this->morphMany(Attachment::class, 'attachable');
  }

  /**
   * Check if the workflow stage is overdue.
   *
   * @return bool
   */
  public function isOverdue(): bool
  {
    // If the stage is completed, it's not overdue
    if ($this->status === 'completed') {
      return false;
    }

    // Check if the deadline has passed
    if ($this->deadline && $this->deadline->isPast()) {
      return true;
    }

    return false;
  }

  /**
   * Mark the workflow stage as complete and advance to next stage.
   *
   * @param string|null $remarks
   * @return void
   */
  public function complete(?string $remarks = null): void
  {
    $this->status = 'completed';
    $this->completed_at = now();

    if ($remarks) {
      $this->remarks = $remarks;
    }

    $this->save();
  }

  /**
   * Return the workflow stage to the previous stage.
   *
   * @param string $remarks
   * @return void
   */
  public function return(string $remarks): void
  {
    $this->status = 'returned';
    $this->remarks = $remarks;
    $this->save();
  }
}

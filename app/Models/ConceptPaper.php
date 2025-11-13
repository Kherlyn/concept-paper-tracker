<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ConceptPaper extends Model
{
  use HasFactory, SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var list<string>
   */
  protected $fillable = [
    'tracking_number',
    'requisitioner_id',
    'department',
    'title',
    'nature_of_request',
    'submitted_at',
    'current_stage_id',
    'status',
    'completed_at',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'submitted_at' => 'datetime',
      'completed_at' => 'datetime',
    ];
  }

  /**
   * Boot the model and set up event listeners.
   */
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($conceptPaper) {
      if (empty($conceptPaper->tracking_number)) {
        $conceptPaper->tracking_number = self::generateTrackingNumber();
      }

      if (empty($conceptPaper->submitted_at)) {
        $conceptPaper->submitted_at = now();
      }

      if (empty($conceptPaper->status)) {
        $conceptPaper->status = 'pending';
      }
    });

    // Delete all attachments when concept paper is deleted
    static::deleting(function ($conceptPaper) {
      // Delete all attachments (which will trigger file deletion via Attachment model)
      $conceptPaper->attachments()->each(function ($attachment) {
        $attachment->delete();
      });
    });
  }

  /**
   * Generate a unique tracking number for the concept paper.
   *
   * @return string
   */
  protected static function generateTrackingNumber(): string
  {
    $prefix = 'CP';
    $year = date('Y');
    $month = date('m');

    // Get the count of papers created this month
    $count = self::whereYear('created_at', $year)
      ->whereMonth('created_at', $month)
      ->count() + 1;

    // Format: CP-YYYY-MM-XXXX (e.g., CP-2025-11-0001)
    $trackingNumber = sprintf('%s-%s-%s-%04d', $prefix, $year, $month, $count);

    // Ensure uniqueness
    while (self::where('tracking_number', $trackingNumber)->exists()) {
      $count++;
      $trackingNumber = sprintf('%s-%s-%s-%04d', $prefix, $year, $month, $count);
    }

    return $trackingNumber;
  }

  /**
   * Relationship: The user who submitted this concept paper.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function requisitioner()
  {
    return $this->belongsTo(User::class, 'requisitioner_id');
  }

  /**
   * Relationship: All workflow stages for this concept paper.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function stages()
  {
    return $this->hasMany(WorkflowStage::class, 'concept_paper_id')->orderBy('stage_order');
  }

  /**
   * Relationship: All attachments for this concept paper.
   *
   * @return \Illuminate\Database\Eloquent\Relations\MorphMany
   */
  public function attachments()
  {
    return $this->morphMany(Attachment::class, 'attachable');
  }

  /**
   * Relationship: All audit logs for this concept paper.
   *
   * @return \Illuminate\Database\Eloquent\Relations\HasMany
   */
  public function auditLogs()
  {
    return $this->hasMany(AuditLog::class, 'concept_paper_id')->orderBy('created_at', 'desc');
  }

  /**
   * Relationship: The current workflow stage.
   *
   * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
   */
  public function currentStage()
  {
    return $this->belongsTo(WorkflowStage::class, 'current_stage_id');
  }

  /**
   * Check if the concept paper has any overdue stages.
   *
   * @return bool
   */
  public function isOverdue(): bool
  {
    // If the paper is completed, it's not overdue
    if ($this->status === 'completed') {
      return false;
    }

    // Check if the current stage is overdue
    if ($this->currentStage && $this->currentStage->isOverdue()) {
      return true;
    }

    // Check if any incomplete stage is overdue
    return $this->stages()
      ->whereIn('status', ['pending', 'in_progress'])
      ->where('deadline', '<', now())
      ->exists();
  }

  /**
   * Check if the concept paper can transition to a specific stage.
   *
   * @param string $toStage
   * @return bool
   */
  public function canTransition(string $toStage): bool
  {
    // Cannot transition if already completed
    if ($this->status === 'completed') {
      return false;
    }

    // If no current stage, can only transition to first stage
    if (!$this->currentStage) {
      return $toStage === 'SPS Review';
    }

    // Get the current stage order
    $currentOrder = $this->currentStage->stage_order;

    // Find the target stage
    $targetStage = $this->stages()->where('stage_name', $toStage)->first();

    if (!$targetStage) {
      return false;
    }

    // Can transition to next stage (current + 1)
    if ($targetStage->stage_order === $currentOrder + 1) {
      return true;
    }

    // Can transition back to previous stage (current - 1)
    if ($targetStage->stage_order === $currentOrder - 1) {
      return true;
    }

    return false;
  }
}

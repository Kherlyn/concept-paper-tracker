<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Attachment extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<string>
   */
  protected $fillable = [
    'attachable_type',
    'attachable_id',
    'file_name',
    'file_path',
    'file_size',
    'mime_type',
    'uploaded_by',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'file_size' => 'integer',
    ];
  }

  /**
   * Relationship: The parent model (ConceptPaper or WorkflowStage).
   *
   * @return MorphTo
   */
  public function attachable(): MorphTo
  {
    return $this->morphTo();
  }

  /**
   * Relationship: The user who uploaded this attachment.
   *
   * @return BelongsTo
   */
  public function uploader(): BelongsTo
  {
    return $this->belongsTo(User::class, 'uploaded_by');
  }

  /**
   * Get the secure URL for accessing the file.
   *
   * @return string
   */
  public function getUrl(): string
  {
    // Generate a route to the download controller with authorization check
    return route('attachments.download', ['attachment' => $this->id]);
  }

  /**
   * Boot the model and set up event listeners.
   */
  protected static function boot()
  {
    parent::boot();

    // Delete the physical file when the attachment record is deleted
    static::deleting(function ($attachment) {
      $storageDisk = config('upload.storage_disk', 'concept_papers');

      if (Storage::disk($storageDisk)->exists($attachment->file_path)) {
        Storage::disk($storageDisk)->delete($attachment->file_path);
      }
    });
  }
}

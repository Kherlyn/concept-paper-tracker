<?php

namespace App\Notifications;

use App\Models\ConceptPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApprovalNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(
    public ConceptPaper $conceptPaper
  ) {}

  /**
   * Get the notification's delivery channels.
   *
   * @return array<int, string>
   */
  public function via(object $notifiable): array
  {
    return ['database', 'mail'];
  }

  /**
   * Get the mail representation of the notification.
   */
  public function toMail(object $notifiable): MailMessage
  {
    return (new MailMessage)
      ->subject('Concept Paper Approved: ' . $this->conceptPaper->title)
      ->markdown('mail.approval-notification', [
        'notifiable' => $notifiable,
        'conceptPaper' => $this->conceptPaper,
      ])
      ->text('mail.approval-notification.text', [
        'notifiable' => $notifiable,
        'conceptPaper' => $this->conceptPaper,
      ]);
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    $processingTime = null;
    if ($this->conceptPaper->submitted_at && $this->conceptPaper->completed_at) {
      $processingTime = $this->conceptPaper->submitted_at->diffInDays($this->conceptPaper->completed_at);
    }

    return [
      'type' => 'approval',
      'concept_paper_id' => $this->conceptPaper->id,
      'concept_paper_title' => $this->conceptPaper->title,
      'tracking_number' => $this->conceptPaper->tracking_number,
      'completed_at' => $this->conceptPaper->completed_at?->toIso8601String(),
      'processing_time_days' => $processingTime,
      'message' => 'Concept paper "' . $this->conceptPaper->title . '" has been approved and completed',
    ];
  }
}

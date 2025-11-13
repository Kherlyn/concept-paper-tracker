<?php

namespace App\Notifications;

use App\Models\ConceptPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaperCompletedNotification extends Notification implements ShouldQueue
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
      ->subject('Concept Paper Completed: ' . $this->conceptPaper->title)
      ->markdown('mail.paper-completed', [
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
    return [
      'type' => 'paper_completed',
      'concept_paper_id' => $this->conceptPaper->id,
      'concept_paper_title' => $this->conceptPaper->title,
      'tracking_number' => $this->conceptPaper->tracking_number,
      'completed_at' => $this->conceptPaper->completed_at->toIso8601String(),
      'message' => 'Concept paper "' . $this->conceptPaper->title . '" has been completed',
    ];
  }
}

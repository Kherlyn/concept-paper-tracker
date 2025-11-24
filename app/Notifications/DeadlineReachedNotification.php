<?php

namespace App\Notifications;

use App\Models\ConceptPaper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineReachedNotification extends Notification implements ShouldQueue
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
      ->subject('Deadline Reached: ' . $this->conceptPaper->title)
      ->markdown('mail.deadline-reached', [
        'notifiable' => $notifiable,
        'conceptPaper' => $this->conceptPaper,
      ])
      ->text('mail.deadline-reached.text', [
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
      'type' => 'deadline_reached',
      'concept_paper_id' => $this->conceptPaper->id,
      'concept_paper_title' => $this->conceptPaper->title,
      'tracking_number' => $this->conceptPaper->tracking_number,
      'current_stage' => $this->conceptPaper->currentStage?->stage_name,
      'deadline_date' => $this->conceptPaper->deadline_date?->toIso8601String(),
      'message' => 'Concept paper "' . $this->conceptPaper->title . '" has reached its deadline',
    ];
  }
}

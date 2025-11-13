<?php

namespace App\Notifications;

use App\Models\WorkflowStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StageAssignedNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(
    public WorkflowStage $stage
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
    $conceptPaper = $this->stage->conceptPaper;

    return (new MailMessage)
      ->subject('New Stage Assignment: ' . $this->stage->stage_name)
      ->markdown('mail.stage-assigned', [
        'notifiable' => $notifiable,
        'stage' => $this->stage,
        'conceptPaper' => $conceptPaper,
      ]);
  }

  /**
   * Get the array representation of the notification.
   *
   * @return array<string, mixed>
   */
  public function toArray(object $notifiable): array
  {
    $conceptPaper = $this->stage->conceptPaper;

    return [
      'type' => 'stage_assigned',
      'stage_id' => $this->stage->id,
      'stage_name' => $this->stage->stage_name,
      'concept_paper_id' => $conceptPaper->id,
      'concept_paper_title' => $conceptPaper->title,
      'tracking_number' => $conceptPaper->tracking_number,
      'deadline' => $this->stage->deadline->toIso8601String(),
      'message' => 'You have been assigned to ' . $this->stage->stage_name . ' for "' . $conceptPaper->title . '"',
    ];
  }
}

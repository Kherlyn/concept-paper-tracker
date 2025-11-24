<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\WorkflowStage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StageReassignmentNotification extends Notification implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new notification instance.
   */
  public function __construct(
    public WorkflowStage $stage,
    public User $previousUser
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
      ->subject('Stage Reassigned: ' . $this->stage->stage_name)
      ->markdown('mail.stage-reassignment', [
        'notifiable' => $notifiable,
        'stage' => $this->stage,
        'conceptPaper' => $conceptPaper,
        'previousUser' => $this->previousUser,
      ])
      ->text('mail.stage-reassignment.text', [
        'notifiable' => $notifiable,
        'stage' => $this->stage,
        'conceptPaper' => $conceptPaper,
        'previousUser' => $this->previousUser,
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
      'type' => 'stage_reassignment',
      'stage_id' => $this->stage->id,
      'stage_name' => $this->stage->stage_name,
      'concept_paper_id' => $conceptPaper->id,
      'concept_paper_title' => $conceptPaper->title,
      'tracking_number' => $conceptPaper->tracking_number,
      'previous_user_name' => $this->previousUser->name,
      'deadline' => $this->stage->deadline?->toIso8601String(),
      'message' => 'Stage "' . $this->stage->stage_name . '" for "' . $conceptPaper->title . '" has been reassigned to you',
    ];
  }
}

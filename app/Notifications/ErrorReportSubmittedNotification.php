<?php

namespace App\Notifications;

use App\Models\ErrorReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class ErrorReportSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public ErrorReport $errorReport;

    /**
     * Create a new notification instance.
     */
    public function __construct(ErrorReport $errorReport)
    {
        $this->errorReport = $errorReport;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Error Report Submitted')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new error report has been submitted by ' . $this->errorReport->user->name . '.')
            ->line('**Title:** ' . $this->errorReport->title)
            ->line('**Description:** ' . substr($this->errorReport->description, 0, 200) . '...')
            ->action('View Error Reports', url('/admin/error-reports'))
            ->line('Please review and address this issue as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'error_report_submitted',
            'error_report_id' => $this->errorReport->id,
            'title' => $this->errorReport->title,
            'submitted_by' => $this->errorReport->user->name,
            'message' => 'New error report: ' . $this->errorReport->title,
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'error_report_submitted',
            'error_report_id' => $this->errorReport->id,
            'title' => $this->errorReport->title,
            'submitted_by' => $this->errorReport->user->name,
            'message' => 'New error report: ' . $this->errorReport->title,
        ]);
    }
}

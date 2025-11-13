# Email Notifications

This document describes the email notification system for the Concept Paper Tracker application.

## Overview

The application sends automated email notifications for key workflow events:

1. **Stage Assignment** - When a workflow stage is assigned to a user
2. **Stage Overdue** - When a stage deadline has passed
3. **Paper Completed** - When a concept paper completes all workflow stages
4. **Paper Returned** - When a concept paper is returned to a previous stage

## Configuration

### Mail Settings

Configure mail settings in your `.env` file:

```env
# For development (logs emails to storage/logs/laravel.log)
MAIL_MAILER=log

# For production (SMTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@conceptpapertracker.local"
MAIL_FROM_NAME="Concept Paper Tracker"
```

### Queue Configuration

Email notifications are sent asynchronously using Laravel's queue system. The queue is configured to use the database driver by default:

```env
QUEUE_CONNECTION=database
```

To process queued emails, run the queue worker:

```bash
php artisan queue:work
```

For production, use a process manager like Supervisor to keep the queue worker running.

## Notification Types

### 1. Stage Assigned Notification

**Trigger:** When a workflow stage is assigned to a user
**Recipients:** The assigned user
**Template:** `resources/views/mail/stage-assigned.blade.php`

**Contains:**

-   Concept paper title and tracking number
-   Stage name
-   Deadline
-   Link to view the concept paper

### 2. Stage Overdue Notification

**Trigger:** When a stage deadline has passed (checked hourly by scheduled job)
**Recipients:** The assigned user
**Template:** `resources/views/mail/stage-overdue.blade.php`

**Contains:**

-   Concept paper title and tracking number
-   Stage name
-   Original deadline
-   Overdue status indicator
-   Link to view the concept paper

### 3. Paper Completed Notification

**Trigger:** When all workflow stages are completed
**Recipients:** The requisitioner
**Template:** `resources/views/mail/paper-completed.blade.php`

**Contains:**

-   Concept paper title and tracking number
-   Department
-   Completion timestamp
-   Link to view the concept paper

### 4. Paper Returned Notification

**Trigger:** When a concept paper is returned to a previous stage
**Recipients:** The user responsible for the previous stage
**Template:** `resources/views/mail/paper-returned.blade.php`

**Contains:**

-   Concept paper title and tracking number
-   Stage it was returned from
-   Remarks explaining why it was returned
-   Link to view the concept paper

## Testing Email Notifications

### Using the Test Command

A test command is provided to send sample notifications:

```bash
# Test all notification types
php artisan notification:test all

# Test specific notification type
php artisan notification:test assigned
php artisan notification:test overdue
php artisan notification:test completed
php artisan notification:test returned
```

### Using Automated Tests

Run the email notification tests:

```bash
php artisan test --filter=EmailNotificationTest
```

### Viewing Email Content in Development

When using `MAIL_MAILER=log`, emails are written to `storage/logs/laravel.log`. You can view them there to verify content and formatting.

## Email Templates

Email templates use Laravel's Markdown mail components for consistent styling. Templates are located in `resources/views/mail/`.

### Customizing Templates

To customize email templates, edit the Blade files in `resources/views/mail/`:

-   `stage-assigned.blade.php`
-   `stage-overdue.blade.php`
-   `paper-completed.blade.php`
-   `paper-returned.blade.php`

### Customizing Email Styling

To customize the overall email styling, publish Laravel's mail components:

```bash
php artisan vendor:publish --tag=laravel-mail
```

This will create customizable templates in `resources/views/vendor/mail/`.

## Production Deployment

### SMTP Configuration

For production, configure a reliable SMTP service:

**Popular Options:**

-   Mailgun
-   SendGrid
-   Amazon SES
-   Postmark
-   Mailtrap (for staging)

Example SMTP configuration:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=your-mailgun-username
MAIL_PASSWORD=your-mailgun-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Concept Paper Tracker"
```

### Queue Worker Setup

In production, use a process manager to keep the queue worker running:

**Supervisor Configuration Example:**

```ini
[program:concept-paper-tracker-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/storage/logs/worker.log
stopwaitsecs=3600
```

### Monitoring

Monitor queue health:

```bash
# Check queue status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

## Troubleshooting

### Emails Not Sending

1. Check queue is running: `php artisan queue:work`
2. Check failed jobs: `php artisan queue:failed`
3. Verify mail configuration in `.env`
4. Check logs: `storage/logs/laravel.log`

### Emails Going to Spam

1. Configure SPF, DKIM, and DMARC records for your domain
2. Use a reputable SMTP service
3. Ensure `MAIL_FROM_ADDRESS` uses your domain
4. Avoid spam trigger words in email content

### Queue Jobs Failing

1. Check error logs: `storage/logs/laravel.log`
2. Verify database connection
3. Ensure models and relationships are properly loaded
4. Check that notification classes implement `ShouldQueue`

## Related Files

-   Notification Classes: `app/Notifications/`
-   Email Templates: `resources/views/mail/`
-   Test Command: `app/Console/Commands/TestEmailNotification.php`
-   Tests: `tests/Feature/EmailNotificationTest.php`
-   Configuration: `config/mail.php`, `config/queue.php`

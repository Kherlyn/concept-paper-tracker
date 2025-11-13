# Email Notification Setup Guide

This guide provides step-by-step instructions for setting up email notifications in the Concept Paper Tracker application.

## Quick Start

### Development Setup (Log Driver)

For local development, emails are logged to `storage/logs/laravel.log` by default:

```bash
# .env configuration (already set)
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@conceptpapertracker.local"
MAIL_FROM_NAME="${APP_NAME}"
```

No additional setup required! Emails will be written to the log file.

### Testing Email Notifications

1. **Run the test command:**

    ```bash
    php artisan notification:test all
    ```

2. **Check the log file:**

    ```bash
    # Windows
    type storage\logs\laravel.log | findstr "Subject:"

    # Linux/Mac
    tail -f storage/logs/laravel.log | grep "Subject:"
    ```

3. **Run automated tests:**
    ```bash
    php artisan test --filter=EmailNotificationTest
    php artisan test --filter=EmailContentTest
    ```

## Production Setup

### Option 1: SMTP (Recommended)

Configure your SMTP server in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Concept Paper Tracker"
```

### Option 2: Mailgun

1. Sign up at [mailgun.com](https://www.mailgun.com)
2. Get your API credentials
3. Configure `.env`:

```env
MAIL_MAILER=mailgun
MAILGUN_DOMAIN=your-domain.mailgun.org
MAILGUN_SECRET=your-api-key
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Concept Paper Tracker"
```

### Option 3: Amazon SES

1. Set up SES in AWS Console
2. Get your credentials
3. Configure `.env`:

```env
MAIL_MAILER=ses
AWS_ACCESS_KEY_ID=your-access-key
AWS_SECRET_ACCESS_KEY=your-secret-key
AWS_DEFAULT_REGION=us-east-1
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="Concept Paper Tracker"
```

### Option 4: Mailtrap (Staging/Testing)

Perfect for staging environments:

1. Sign up at [mailtrap.io](https://mailtrap.io)
2. Get your inbox credentials
3. Configure `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@conceptpapertracker.local"
MAIL_FROM_NAME="Concept Paper Tracker"
```

## Queue Configuration

Email notifications are sent asynchronously using queues. This prevents delays in the application.

### Development

```bash
# Start the queue worker
php artisan queue:work

# Or use queue:listen for auto-reloading during development
php artisan queue:listen
```

### Production

Use a process manager like Supervisor to keep the queue worker running:

**Supervisor Configuration (`/etc/supervisor/conf.d/concept-paper-tracker.conf`):**

```ini
[program:concept-paper-tracker-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/concept-paper-tracker/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/concept-paper-tracker/storage/logs/worker.log
stopwaitsecs=3600
```

**Start Supervisor:**

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start concept-paper-tracker-worker:*
```

## Email Templates

All email templates are located in `resources/views/mail/`:

-   `stage-assigned.blade.php` - New stage assignment notification
-   `stage-overdue.blade.php` - Overdue stage alert
-   `paper-completed.blade.php` - Concept paper completion notification
-   `paper-returned.blade.php` - Paper returned notification

### Customizing Email Templates

Edit the Blade files directly to customize content. They use Laravel's Markdown mail components for consistent styling.

### Customizing Email Styling

To customize colors, fonts, and overall styling:

```bash
php artisan vendor:publish --tag=laravel-mail
```

This creates customizable templates in `resources/views/vendor/mail/`.

## Notification Types

### 1. Stage Assigned

-   **Sent to:** User assigned to the new stage
-   **Trigger:** When workflow advances to next stage
-   **Contains:** Paper details, stage name, deadline, action link

### 2. Stage Overdue

-   **Sent to:** User assigned to overdue stage
-   **Trigger:** Hourly check finds stages past deadline
-   **Contains:** Paper details, stage name, overdue indicator, action link

### 3. Paper Completed

-   **Sent to:** Original requisitioner
-   **Trigger:** When final stage (Budget Release) is completed
-   **Contains:** Paper details, completion timestamp, view link

### 4. Paper Returned

-   **Sent to:** User responsible for previous stage
-   **Trigger:** When approver returns paper to previous stage
-   **Contains:** Paper details, remarks, action link

## Troubleshooting

### Emails Not Sending

**Check queue is running:**

```bash
php artisan queue:work
```

**Check failed jobs:**

```bash
php artisan queue:failed
php artisan queue:retry all
```

**Check logs:**

```bash
tail -f storage/logs/laravel.log
```

### Testing SMTP Connection

```bash
php artisan tinker
```

```php
Mail::raw('Test email', function ($message) {
    $message->to('test@example.com')
            ->subject('Test Email');
});
```

### Emails Going to Spam

1. **Configure DNS records:**

    - SPF: `v=spf1 include:_spf.yourmailprovider.com ~all`
    - DKIM: Get from your mail provider
    - DMARC: `v=DMARC1; p=quarantine; rua=mailto:dmarc@yourdomain.com`

2. **Use authenticated SMTP**
3. **Use your domain in FROM address**
4. **Avoid spam trigger words**

### Queue Jobs Timing Out

Increase timeout in queue worker:

```bash
php artisan queue:work --timeout=300
```

Or in Supervisor config:

```ini
command=php artisan queue:work --timeout=300 --tries=3
```

## Monitoring

### Check Queue Status

```bash
# View queue statistics
php artisan queue:monitor

# Check for failed jobs
php artisan queue:failed

# Clear failed jobs
php artisan queue:flush
```

### Monitor Email Delivery

For production, consider using:

-   **Mailgun Dashboard** - Delivery statistics and logs
-   **Amazon SES Console** - Bounce and complaint tracking
-   **Postmark** - Detailed delivery analytics

## Security Best Practices

1. **Never commit credentials** - Use `.env` file (already in `.gitignore`)
2. **Use app passwords** - For Gmail, use app-specific passwords
3. **Enable 2FA** - On your email service account
4. **Rotate credentials** - Regularly update passwords
5. **Monitor bounces** - Set up bounce handling
6. **Rate limiting** - Configure sending limits to avoid blacklisting

## Performance Optimization

### Use Redis for Queues (Production)

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Batch Notifications

For bulk notifications, use Laravel's notification batching:

```php
Notification::send($users, new StageAssignedNotification($stage));
```

### Monitor Queue Depth

Set up alerts when queue depth exceeds threshold:

```bash
php artisan queue:monitor redis:default --max=100
```

## Related Documentation

-   [Email Notifications Overview](EMAIL_NOTIFICATIONS.md)
-   [Laravel Mail Documentation](https://laravel.com/docs/mail)
-   [Laravel Queue Documentation](https://laravel.com/docs/queues)
-   [Laravel Notifications Documentation](https://laravel.com/docs/notifications)

## Support

For issues or questions:

1. Check logs: `storage/logs/laravel.log`
2. Run tests: `php artisan test --filter=Email`
3. Test manually: `php artisan notification:test`
4. Review configuration: `config/mail.php`

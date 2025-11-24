# Queue and Scheduler Setup Guide

This guide provides step-by-step instructions for setting up and managing the queue workers and task scheduler in the Concept Paper Tracker system.

## Overview

The system uses two key Laravel features for background processing:

1. **Queue System** - Processes asynchronous jobs (document conversion, email notifications)
2. **Task Scheduler** - Runs scheduled tasks automatically (deadline checks, overdue monitoring)

## Quick Start

### Development Environment

**Option 1: Use the combined dev script (Recommended)**

```bash
composer dev
```

This starts the Laravel server, queue worker, and Vite dev server concurrently.

**Option 2: Run services separately**

```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start queue worker
php artisan queue:work

# Terminal 3: Start scheduler (for hourly tasks)
php artisan schedule:work

# Terminal 4: Start Vite dev server
npm run dev
```

## Queue System Setup

### 1. Verify Queue Configuration

Check your `.env` file:

```env
QUEUE_CONNECTION=database
```

The system uses the `database` driver by default, which stores jobs in the `jobs` table.

### 2. Ensure Database Tables Exist

The queue tables are created during migration:

```bash
php artisan migrate
```

Tables created:

-   `jobs` - Pending and processing jobs
-   `failed_jobs` - Failed jobs for debugging
-   `job_batches` - Batch job tracking

### 3. Start Queue Worker

**Development:**

```bash
php artisan queue:work
```

**With verbose output:**

```bash
php artisan queue:work --verbose
```

**Process specific queue:**

```bash
php artisan queue:work --queue=emails,default
```

### 4. Queue Worker Options

```bash
# Run with specific number of tries
php artisan queue:work --tries=3

# Set timeout for jobs
php artisan queue:work --timeout=90

# Process jobs then exit (useful for testing)
php artisan queue:work --once

# Stop after processing all jobs
php artisan queue:work --stop-when-empty
```

## Task Scheduler Setup

### 1. Verify Scheduled Tasks

List all scheduled tasks:

```bash
php artisan schedule:list
```

Expected output:

```
0 * * * *  App\Jobs\CheckOverdueStages ........ Next Due: X minutes from now
0 * * * *  App\Jobs\CheckDeadlinesJob ......... Next Due: X minutes from now
```

### 2. Run Scheduler in Development

**Option 1: Use schedule:work (Laravel 11+)**

```bash
php artisan schedule:work
```

This runs the scheduler in the foreground, checking for due tasks every minute.

**Option 2: Manual testing**

```bash
php artisan schedule:run
```

This runs all due tasks immediately (useful for testing).

### 3. Test Specific Scheduled Task

```bash
# Test the deadline check job
php artisan schedule:test --name="App\Jobs\CheckDeadlinesJob"
```

## Background Jobs

### Available Jobs

1. **ConvertDocumentJob**

    - Purpose: Convert Word documents to PDF
    - Trigger: Automatic on Word document upload
    - Retries: 3 attempts with exponential backoff
    - Timeout: 90 seconds

2. **SendDeadlineNotificationJob**

    - Purpose: Send deadline reached notifications
    - Trigger: Dispatched by CheckDeadlinesJob
    - Retries: 3 attempts
    - Queue: emails (optional)

3. **SendApprovalNotificationJob**

    - Purpose: Send approval completion notifications
    - Trigger: When all workflow stages complete
    - Retries: 3 attempts
    - Queue: emails (optional)

4. **CheckOverdueStages** (Scheduled)

    - Purpose: Check for overdue workflow stages
    - Schedule: Hourly (0 \* \* \* \*)
    - Sends notifications for overdue stages

5. **CheckDeadlinesJob** (Scheduled)
    - Purpose: Check for reached deadlines
    - Schedule: Hourly (0 \* \* \* \*)
    - Dispatches notification jobs for reached deadlines

### Manually Dispatch Jobs

```bash
# Test document conversion
php artisan tinker
>>> $attachment = App\Models\Attachment::first();
>>> App\Jobs\ConvertDocumentJob::dispatch($attachment);

# Test deadline check
>>> App\Jobs\CheckDeadlinesJob::dispatch();
```

## Monitoring and Debugging

### View Queue Status

```bash
# List all jobs in queue
php artisan queue:monitor database

# View failed jobs
php artisan queue:failed

# View failed job details
php artisan queue:failed --id=1
```

### Retry Failed Jobs

```bash
# Retry all failed jobs
php artisan queue:retry all

# Retry specific job
php artisan queue:retry 1

# Retry jobs that failed in last hour
php artisan queue:retry --range=1-100
```

### Clear Failed Jobs

```bash
# Clear all failed jobs
php artisan queue:flush

# Clear specific failed job
php artisan queue:forget 1
```

### Restart Queue Workers

```bash
# Gracefully restart all queue workers
php artisan queue:restart
```

This sends a signal to workers to finish their current job and restart.

### View Logs

```bash
# View Laravel logs (includes queue and scheduler logs)
tail -f storage/logs/laravel.log

# Filter for queue-related logs
tail -f storage/logs/laravel.log | grep -i queue

# Filter for scheduler-related logs
tail -f storage/logs/laravel.log | grep -i schedule
```

## Production Setup

### 1. Configure Supervisor for Queue Workers

Create `/etc/supervisor/conf.d/concept-paper-tracker-worker.conf`:

```ini
[program:concept-paper-tracker-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/concept-paper-tracker/artisan queue:work database --sleep=3 --tries=3 --max-time=3600 --timeout=90
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

**Monitor Supervisor:**

```bash
sudo supervisorctl status
sudo supervisorctl tail -f concept-paper-tracker-worker:0 stdout
```

### 2. Configure Cron for Scheduler

Add to crontab:

```bash
crontab -e
```

Add this line:

```bash
* * * * * cd /var/www/concept-paper-tracker && php artisan schedule:run >> /dev/null 2>&1
```

**Verify cron is running:**

```bash
# Check cron service
sudo systemctl status cron

# View cron logs
grep CRON /var/log/syslog
```

### 3. Production Environment Variables

Update `.env` for production:

```env
APP_ENV=production
APP_DEBUG=false
QUEUE_CONNECTION=database  # or redis for better performance
LOG_LEVEL=warning
```

### 4. Optimize for Production

```bash
# Cache configuration
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache

# Optimize autoloader
composer install --optimize-autoloader --no-dev
```

## Performance Tuning

### Queue Performance

**Use Redis for high-volume applications:**

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

**Increase worker processes:**

```ini
# In supervisor config
numprocs=4  # Increase based on server capacity
```

**Adjust worker settings:**

```bash
# Process more jobs before restarting
php artisan queue:work --max-jobs=1000

# Increase memory limit
php artisan queue:work --memory=256
```

### Scheduler Performance

**Prevent overlapping:**

```php
// In routes/console.php
Schedule::job(new CheckDeadlinesJob)
    ->hourly()
    ->withoutOverlapping();
```

**Run on specific servers:**

```php
Schedule::job(new CheckDeadlinesJob)
    ->hourly()
    ->onOneServer();
```

## Troubleshooting

### Queue Worker Not Processing Jobs

**Check if worker is running:**

```bash
ps aux | grep "queue:work"
```

**Check database connection:**

```bash
php artisan tinker
>>> DB::connection()->getPdo();
```

**Check jobs table:**

```bash
php artisan tinker
>>> DB::table('jobs')->count();
```

**Restart worker:**

```bash
php artisan queue:restart
```

### Scheduled Tasks Not Running

**Verify cron is configured:**

```bash
crontab -l
```

**Test schedule manually:**

```bash
php artisan schedule:run
```

**Check scheduler logs:**

```bash
tail -f storage/logs/laravel.log | grep schedule
```

**Verify task schedule:**

```bash
php artisan schedule:list
```

### Jobs Failing Repeatedly

**View failed job details:**

```bash
php artisan queue:failed
```

**Check error logs:**

```bash
tail -f storage/logs/laravel.log
```

**Common issues:**

-   Database connection timeout
-   Memory limit exceeded
-   File permissions
-   Missing dependencies
-   Timeout exceeded

**Solutions:**

-   Increase timeout: `--timeout=120`
-   Increase memory: `--memory=512`
-   Check file permissions: `chmod -R 775 storage`
-   Verify dependencies: `composer install`

### High Queue Backlog

**Check queue depth:**

```bash
php artisan tinker
>>> DB::table('jobs')->count();
```

**Solutions:**

-   Increase worker processes
-   Switch to Redis queue driver
-   Optimize job processing
-   Add more server resources

## Best Practices

### Development

1. Always run queue worker during development
2. Use `schedule:work` to test scheduled tasks
3. Monitor logs for errors
4. Test jobs manually before deploying
5. Use `--once` flag for debugging specific jobs

### Production

1. Use Supervisor for queue workers
2. Configure proper logging and monitoring
3. Set up alerts for failed jobs
4. Regular backup of failed_jobs table
5. Monitor queue depth and processing time
6. Use Redis for better performance
7. Implement job rate limiting if needed
8. Set up proper error notifications

### Code Quality

1. Always implement retry logic with exponential backoff
2. Set appropriate timeouts for jobs
3. Log important events and errors
4. Handle exceptions gracefully
5. Use job middleware for common functionality
6. Test jobs thoroughly before deployment
7. Document job dependencies and requirements

## Additional Resources

-   [Laravel Queue Documentation](https://laravel.com/docs/queues)
-   [Laravel Scheduler Documentation](https://laravel.com/docs/scheduling)
-   [Supervisor Documentation](http://supervisord.org/)
-   [Redis Documentation](https://redis.io/documentation)

## Support

For issues with queue or scheduler:

1. Check logs: `storage/logs/laravel.log`
2. Review failed jobs: `php artisan queue:failed`
3. Test manually: `php artisan schedule:run`
4. Contact development team with error details

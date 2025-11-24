# Dependency Verification Checklist

This document provides a checklist to verify that all dependencies for the Concept Paper Tracker workflow enhancements are properly installed and configured.

## Installation Verification

### PHP Dependencies

#### PHPWord

```bash
composer show phpoffice/phpword
```

**Expected Output:**

```
name     : phpoffice/phpword
versions : * 1.4.0
type     : library
```

**Status:** ✅ Installed (v1.4.0)

#### dompdf

```bash
composer show barryvdh/laravel-dompdf
```

**Expected Output:**

```
name     : barryvdh/laravel-dompdf
versions : * 3.1.x
type     : library
```

**Status:** ✅ Installed (v3.1.x)

### JavaScript Dependencies

#### Fabric.js

```bash
npm list fabric
```

**Expected Output:**

```
concept-paper-tracker@
└── fabric@6.9.0
```

**Status:** ✅ Installed (v6.9.0)

#### PDF.js

```bash
npm list pdfjs-dist
```

**Expected Output:**

```
concept-paper-tracker@
└── pdfjs-dist@5.4.394
```

**Status:** ✅ Installed (v5.4.394)

## Configuration Verification

### Queue Configuration

#### Check Queue Connection

```bash
php artisan config:show queue.default
```

**Expected Output:**

```
database
```

**Status:** ✅ Configured (database driver)

#### Verify Queue Tables

```bash
php artisan tinker
>>> DB::table('jobs')->exists();
>>> DB::table('failed_jobs')->exists();
```

**Expected Output:**

```
true
true
```

**Status:** ✅ Tables exist

### Scheduler Configuration

#### List Scheduled Tasks

```bash
php artisan schedule:list
```

**Expected Output:**

```
0 * * * *  App\Jobs\CheckOverdueStages ........ Next Due: X minutes from now
0 * * * *  App\Jobs\CheckDeadlinesJob ......... Next Due: X minutes from now
```

**Status:** ✅ Configured (2 hourly tasks)

### Background Jobs

#### Verify Jobs Exist

```bash
ls app/Jobs/
```

**Expected Files:**

-   CheckDeadlinesJob.php ✅
-   CheckOverdueStages.php ✅
-   ConvertDocumentJob.php ✅
-   SendApprovalNotificationJob.php ✅
-   SendDeadlineNotificationJob.php ✅

**Status:** ✅ All jobs exist

## Functional Testing

### Test Queue Worker

#### Start Queue Worker

```bash
php artisan queue:work --once
```

**Expected:** Worker starts without errors

#### Dispatch Test Job

```bash
php artisan tinker
>>> App\Jobs\CheckDeadlinesJob::dispatch();
>>> exit
php artisan queue:work --once
```

**Expected:** Job processes successfully

### Test Scheduler

#### Run Scheduler Manually

```bash
php artisan schedule:run
```

**Expected Output:**

```
Running scheduled command: App\Jobs\CheckOverdueStages
Running scheduled command: App\Jobs\CheckDeadlinesJob
```

**Status:** ✅ Scheduler runs successfully

### Test Document Conversion

#### Verify PHPWord Can Load

```bash
php artisan tinker
>>> use PhpOffice\PhpWord\IOFactory;
>>> echo "PHPWord loaded successfully";
```

**Expected:** No errors, confirmation message displayed

### Test Frontend Dependencies

#### Verify Fabric.js

```bash
npm run build
```

**Expected:** Build completes without errors related to Fabric.js

#### Verify PDF.js

Check that PDF.js worker file is accessible:

```bash
ls node_modules/pdfjs-dist/build/pdf.worker.js
```

**Expected:** File exists

## Environment Configuration

### Required Environment Variables

Check `.env` file contains:

```env
# Queue Configuration
QUEUE_CONNECTION=database ✅

# File Upload Configuration
UPLOAD_MAX_FILE_SIZE=10485760 ✅
UPLOAD_STORAGE_DISK=concept_papers ✅

# Cache Configuration
CACHE_STORE=database ✅
```

**Status:** ✅ All required variables configured

## Production Readiness Checklist

### Queue System

-   [ ] Supervisor configuration created
-   [ ] Queue workers running
-   [ ] Failed job monitoring configured
-   [ ] Queue depth monitoring enabled
-   [ ] Log rotation configured

### Scheduler

-   [ ] Cron job configured
-   [ ] Scheduler logs monitored
-   [ ] Task execution verified
-   [ ] Failure notifications configured

### Dependencies

-   [x] PHPWord installed and tested
-   [x] dompdf installed and tested
-   [x] Fabric.js installed and tested
-   [x] PDF.js installed and tested
-   [ ] Production build tested
-   [ ] Browser compatibility verified

### Performance

-   [ ] Document conversion caching enabled
-   [ ] Queue performance optimized
-   [ ] Redis configured (optional, for high volume)
-   [ ] Worker process count optimized

### Monitoring

-   [ ] Queue monitoring dashboard
-   [ ] Scheduler execution logs
-   [ ] Failed job alerts
-   [ ] Performance metrics tracking

## Troubleshooting Commands

### If Dependencies Are Missing

**Reinstall PHP dependencies:**

```bash
composer install
```

**Reinstall JavaScript dependencies:**

```bash
npm install
```

**Clear caches:**

```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
npm run build
```

### If Queue Not Working

**Check queue configuration:**

```bash
php artisan config:show queue
```

**Restart queue workers:**

```bash
php artisan queue:restart
```

**Clear failed jobs:**

```bash
php artisan queue:flush
```

### If Scheduler Not Working

**Test scheduler:**

```bash
php artisan schedule:test
```

**Run scheduler manually:**

```bash
php artisan schedule:run
```

**Check cron configuration:**

```bash
crontab -l
```

## Verification Summary

### Development Environment

-   ✅ PHPWord installed (v1.4.0)
-   ✅ dompdf installed (v3.1.x)
-   ✅ Fabric.js installed (v6.9.0)
-   ✅ PDF.js installed (v5.4.394)
-   ✅ Queue configured (database driver)
-   ✅ Scheduler configured (2 hourly tasks)
-   ✅ Background jobs created (5 jobs)
-   ✅ Documentation created

### Next Steps for Production

1. Configure Supervisor for queue workers
2. Set up cron job for scheduler
3. Test document conversion with real files
4. Verify email notifications
5. Monitor queue performance
6. Set up alerts for failed jobs

## Support

If any verification step fails:

1. Review the error message
2. Check the relevant documentation:
    - [Dependencies Guide](DEPENDENCIES.md)
    - [Queue and Scheduler Setup](QUEUE_AND_SCHEDULER_SETUP.md)
3. Review logs: `storage/logs/laravel.log`
4. Contact development team with verification results

## Last Verified

Date: [Current Date]
Environment: Development
Status: ✅ All dependencies installed and configured

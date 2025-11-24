# Dependencies and Configuration

This document provides detailed information about the external dependencies used in the Concept Paper Tracker system and their configuration.

## PHP Dependencies

### PHPWord (phpoffice/phpword)

**Version:** ^1.4  
**Purpose:** Reading and writing Word documents (.doc, .docx)  
**Usage:** Converting uploaded Word documents to PDF for in-browser preview

**Installation:**

```bash
composer require phpoffice/phpword
```

**Configuration:**

-   No additional configuration required
-   Used in `DocumentPreviewService` for document conversion
-   Requires PHP extensions: dom, gd, json, xml, zip

**Features Used:**

-   Reading .docx files
-   Converting to HTML/PDF format
-   Extracting document metadata

**Related Files:**

-   `app/Services/DocumentPreviewService.php`
-   `app/Jobs/ConvertDocumentJob.php`

### dompdf (barryvdh/laravel-dompdf)

**Version:** ^3.1  
**Purpose:** PDF generation from HTML  
**Usage:** Converting Word documents to PDF format for preview

**Installation:**

```bash
composer require barryvdh/laravel-dompdf
```

**Configuration:**

-   Configuration file: `config/dompdf.php` (auto-generated)
-   Default settings are sufficient for document preview
-   Caching enabled for converted PDFs (24-hour TTL)

**Related Files:**

-   `app/Services/DocumentPreviewService.php`
-   `app/Jobs/ConvertDocumentJob.php`

## JavaScript Dependencies

### Fabric.js

**Version:** ^6.9.0  
**Purpose:** Canvas-based graphics and annotation library  
**Usage:** Document annotation tools (markers, highlights, drawings)

**Installation:**

```bash
npm install fabric
```

**Configuration:**

-   No additional configuration required
-   Imported in annotation components

**Features Used:**

-   Canvas rendering
-   Shape drawing (rectangles, circles, freehand)
-   Text annotations
-   JSON serialization for database storage
-   Touch gesture support for mobile devices

**Related Files:**

-   `resources/js/Components/AnnotationCanvas.jsx`
-   `resources/js/Components/DocumentPreviewWithAnnotations.jsx`

**Usage Example:**

```javascript
import { fabric } from "fabric";

const canvas = new fabric.Canvas("annotation-canvas");
canvas.add(
    new fabric.Rect({
        left: 100,
        top: 100,
        fill: "rgba(255, 0, 0, 0.3)",
        width: 50,
        height: 50,
    })
);
```

### PDF.js (pdfjs-dist)

**Version:** ^5.4.394  
**Purpose:** PDF rendering in browser  
**Usage:** Displaying PDF documents in preview modal

**Installation:**

```bash
npm install pdfjs-dist
```

**Configuration:**

-   Worker file must be configured for proper operation
-   Worker path set in component initialization

**Features Used:**

-   PDF document loading
-   Page rendering to canvas
-   Page navigation
-   Zoom and pan controls
-   Text extraction (for search functionality)

**Related Files:**

-   `resources/js/Components/DocumentPreview.jsx`
-   `resources/js/Components/DocumentPreviewWithAnnotations.jsx`

**Usage Example:**

```javascript
import * as pdfjsLib from "pdfjs-dist";

// Configure worker
pdfjsLib.GlobalWorkerOptions.workerSrc = "/path/to/pdf.worker.js";

// Load PDF
const loadingTask = pdfjsLib.getDocument(pdfUrl);
const pdf = await loadingTask.promise;
```

## Queue System

### Laravel Queue

**Driver:** Database (default)  
**Purpose:** Asynchronous job processing  
**Configuration:** `config/queue.php`

**Queue Connections:**

-   `database` - Default queue for all jobs
-   `emails` - Dedicated queue for email notifications (optional)

**Environment Variables:**

```env
QUEUE_CONNECTION=database
DB_QUEUE_TABLE=jobs
DB_QUEUE=default
DB_QUEUE_RETRY_AFTER=90
```

**Queue Jobs:**

1. `ConvertDocumentJob` - Document conversion (3 retries, exponential backoff)
2. `SendDeadlineNotificationJob` - Deadline notifications
3. `SendApprovalNotificationJob` - Approval notifications
4. `CheckOverdueStages` - Scheduled hourly check
5. `CheckDeadlinesJob` - Scheduled hourly check

**Running Queue Workers:**

Development:

```bash
php artisan queue:work
```

Production (with Supervisor):

```ini
[program:concept-paper-tracker-worker]
command=php /path/to/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
numprocs=2
```

## Task Scheduler

### Laravel Scheduler

**Purpose:** Running scheduled tasks automatically  
**Configuration:** `routes/console.php`

**Scheduled Tasks:**

```php
// Check for overdue workflow stages (hourly)
Schedule::job(new CheckOverdueStages)->hourly();

// Check for reached deadlines (hourly)
Schedule::job(new CheckDeadlinesJob)->hourly();
```

**Running Scheduler:**

Development:

```bash
php artisan schedule:work
```

Production (crontab):

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

**Scheduler Features:**

-   Automatic job dispatching at specified intervals
-   Prevents overlapping executions
-   Logs all scheduled task runs
-   Email notifications on task failure (configurable)

## Development Dependencies

### Vite

**Version:** ^7.0.7  
**Purpose:** Frontend build tool and dev server  
**Configuration:** `vite.config.js`

**Features:**

-   Hot module replacement (HMR)
-   Fast builds with esbuild
-   Asset optimization
-   React Fast Refresh

### Tailwind CSS

**Version:** ^3.2.1  
**Purpose:** Utility-first CSS framework  
**Configuration:** `tailwind.config.js`

**Plugins:**

-   `@tailwindcss/forms` - Form styling
-   `@tailwindcss/typography` - Markdown/prose styling

## Production Considerations

### Performance Optimization

1. **Queue Workers:**

    - Use Redis instead of database for high-volume applications
    - Run multiple queue workers (2-4 processes)
    - Monitor queue depth and processing time

2. **Document Conversion:**

    - Cache converted PDFs (24-hour TTL)
    - Consider using dedicated conversion service for large files
    - Implement file size limits (current: 10MB)

3. **PDF.js:**

    - Serve worker file from CDN in production
    - Enable lazy loading for large documents
    - Implement progressive rendering

4. **Fabric.js:**
    - Limit canvas size for performance
    - Debounce annotation save operations
    - Use object caching for complex annotations

### Monitoring

**Queue Monitoring:**

```bash
# Monitor queue in real-time
php artisan queue:monitor

# Check failed jobs
php artisan queue:failed

# View queue statistics
php artisan queue:work --verbose
```

**Scheduler Monitoring:**

```bash
# View scheduled tasks
php artisan schedule:list

# Test scheduled tasks
php artisan schedule:test
```

**Log Files:**

-   Queue logs: `storage/logs/laravel.log`
-   Worker logs: `storage/logs/worker.log` (production)
-   Scheduler logs: `storage/logs/scheduler.log` (production)

## Troubleshooting

### PHPWord Issues

**Problem:** Document conversion fails  
**Solution:**

-   Verify PHP extensions are installed: `php -m | grep -E 'dom|gd|zip|xml'`
-   Check file permissions on storage directory
-   Increase PHP memory limit if needed: `memory_limit=256M`

### Fabric.js Issues

**Problem:** Annotations not saving  
**Solution:**

-   Check browser console for JavaScript errors
-   Verify canvas dimensions match document size
-   Ensure JSON serialization is working

### PDF.js Issues

**Problem:** PDF not rendering  
**Solution:**

-   Verify worker file path is correct
-   Check CORS headers for PDF files
-   Ensure PDF file is not corrupted

### Queue Issues

**Problem:** Jobs not processing  
**Solution:**

-   Verify queue worker is running: `ps aux | grep queue:work`
-   Check database connection
-   Review failed jobs: `php artisan queue:failed`
-   Restart queue worker: `php artisan queue:restart`

### Scheduler Issues

**Problem:** Scheduled tasks not running  
**Solution:**

-   Verify cron job is configured correctly
-   Check scheduler logs
-   Test manually: `php artisan schedule:run`
-   Verify task schedule: `php artisan schedule:list`

## Updating Dependencies

### PHP Dependencies

```bash
# Update all dependencies
composer update

# Update specific package
composer update phpoffice/phpword

# Check for outdated packages
composer outdated
```

### JavaScript Dependencies

```bash
# Update all dependencies
npm update

# Update specific package
npm update fabric

# Check for outdated packages
npm outdated
```

### Security Updates

```bash
# Check for security vulnerabilities
composer audit
npm audit

# Fix vulnerabilities
npm audit fix
```

## Version Compatibility

### PHP Requirements

-   PHP: ^8.2
-   Laravel: ^12.0
-   PHPWord: ^1.4
-   dompdf: ^3.1

### Node.js Requirements

-   Node.js: 18+
-   npm: 9+
-   Fabric.js: ^6.9.0
-   PDF.js: ^5.4.394

### Browser Compatibility

-   Chrome/Edge: Latest 2 versions
-   Firefox: Latest 2 versions
-   Safari: Latest 2 versions
-   Mobile browsers: iOS Safari 14+, Chrome Android 90+

## Additional Resources

### Documentation Links

-   [PHPWord Documentation](https://phpoffice.github.io/PHPWord/)
-   [dompdf Documentation](https://github.com/dompdf/dompdf)
-   [Fabric.js Documentation](http://fabricjs.com/docs/)
-   [PDF.js Documentation](https://mozilla.github.io/pdf.js/)
-   [Laravel Queue Documentation](https://laravel.com/docs/queues)
-   [Laravel Scheduler Documentation](https://laravel.com/docs/scheduling)

### Support

For dependency-related issues:

1. Check the official documentation
2. Search GitHub issues for known problems
3. Review Laravel community forums
4. Contact the development team

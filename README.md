# Concept Paper Tracker

A web application for digitizing and automating the approval workflow for concept papers with budget allocation. Built with Laravel 12, Inertia.js, and React.

## Features

-   **Public Landing Page**: Informative homepage showcasing system features, workflow process, and user roles
-   **Enhanced User Registration**: Capture academic information including school year and student number
-   **Comprehensive User Guide**: In-app documentation with role-specific guides and FAQs
-   **Role-Based Access Control**: Seven distinct user roles (Requisitioner, SPS, VP Acad, Senior VP, Auditor, Accounting, Admin)
-   **10-Stage Approval Workflow**: Automated routing through predefined approval stages
-   **Email Notifications**: Automated notifications for stage assignments, overdue tasks, completions, and returns
-   **Deadline Tracking**: Automatic deadline calculation and overdue alerts
-   **Audit Trail**: Complete history of all actions taken on concept papers
-   **File Attachments**: PDF upload support for concept papers and supporting documents
-   **Admin Dashboard**: User management and comprehensive reporting
-   **Responsive Design**: Works on desktop, tablet, and mobile devices

## Requirements

-   PHP 8.2 or higher
-   Composer
-   Node.js 18+ and npm
-   SQLite (development) or MySQL/PostgreSQL (production)

## Installation

1. **Clone the repository**

```bash
git clone <repository-url>
cd concept-paper-tracker
```

2. **Install PHP dependencies**

```bash
composer install
```

3. **Install JavaScript dependencies**

```bash
npm install
```

4. **Set up environment**

```bash
cp .env.example .env
php artisan key:generate
```

5. **Configure database**

The application uses SQLite by default for development. The database file will be created automatically.

For production, update the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=concept_paper_tracker
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

6. **Run migrations and seeders**

```bash
php artisan migrate --seed
```

This will create the database tables and seed test users with all roles.

7. **Build frontend assets**

```bash
npm run build
```

For development with hot reload:

```bash
npm run dev
```

8. **Start the queue worker**

Email notifications and background jobs (document conversion, deadline checks) are processed asynchronously via queue:

```bash
php artisan queue:work
```

For development, you can use the `dev` script which runs the server, queue worker, and Vite concurrently:

```bash
composer dev
```

9. **Start the scheduler (optional for development)**

The system includes scheduled tasks that run hourly:

-   Check for reached deadlines and send notifications
-   Check for overdue workflow stages

To enable scheduled tasks in development:

```bash
php artisan schedule:work
```

Or add to your crontab for production (see Production Deployment section).

10. **Start the development server**

```bash
php artisan serve
```

Visit `http://localhost:8000` in your browser.

## Landing Page

The application features a public landing page that serves as the entry point for new users.

### Accessing the Landing Page

-   **Unauthenticated users**: Visiting the root URL (`/`) displays the landing page
-   **Authenticated users**: Automatically redirected to the dashboard

### Landing Page Sections

The landing page includes:

1. **Hero Section**: System title, tagline, and call-to-action buttons
2. **Features Section**: Key capabilities displayed in a grid layout
3. **Workflow Section**: Visual representation of the 9-step approval process
4. **Roles Section**: Description of all six user roles
5. **Use Cases Section**: Example scenarios demonstrating system benefits
6. **CTA Section**: Final call-to-action encouraging registration
7. **Footer**: Links and contact information

### Customizing the Landing Page

To customize the landing page content:

1. **Edit the Landing component**: `resources/js/Pages/Landing.jsx`
2. **Modify sub-components**: Located in `resources/js/Pages/Landing/` directory
    - `HeroSection.jsx` - Hero banner and main CTA
    - `FeaturesSection.jsx` - Feature cards
    - `WorkflowSection.jsx` - Workflow visualization
    - `RolesSection.jsx` - User role descriptions
    - `UseCasesSection.jsx` - Example use cases
    - `CTASection.jsx` - Final call-to-action
    - `Footer.jsx` - Footer content
3. **Update styling**: Modify Tailwind CSS classes in component files
4. **Change workflow data**: Edit `config/workflow.php` for workflow stage information

### Landing Page Assets

-   **Icons**: Uses Heroicons (included via `@heroicons/react`)
-   **Styling**: Tailwind CSS with custom gradient backgrounds
-   **Responsive**: Mobile-first design with breakpoints for tablet and desktop

## User Registration

### Academic Information Fields

The registration form captures additional academic information:

-   **School Year** (optional): Academic year designation (e.g., "2024-2025", "1st Year", "2nd Year")
-   **Student Number** (optional, requisitioner role only): Unique student identifier
-   **Department** (required): User's department or organizational unit

### Field Validation

-   **School Year**: Maximum 50 characters, free-form text
-   **Student Number**: Maximum 50 characters, must be unique across all users
-   **Department**: Required for all users

### Role Descriptions

During registration, users can select from the following roles:

-   **Requisitioner**: Submit and track concept papers
-   **SPS**: School Principal/Supervisor - Initial review
-   **VP Acad**: Vice President for Academic Affairs - Academic review
-   **Auditor**: Audit review and countersigning
-   **Accounting**: Voucher and cheque preparation

Note: Admin role can only be assigned by existing administrators.

## Default Test Users

After running the seeders, you can log in with these test accounts:

| Role          | Email                     | Password | School Year | Student Number |
| ------------- | ------------------------- | -------- | ----------- | -------------- |
| Requisitioner | requisitioner@example.com | password | 2024-2025   | 2024-00001     |
| SPS           | sps@example.com           | password | N/A         | N/A            |
| VP Acad       | vp_acad@example.com       | password | N/A         | N/A            |
| Auditor       | auditor@example.com       | password | N/A         | N/A            |
| Accounting    | accounting@example.com    | password | N/A         | N/A            |
| Admin         | admin@example.com         | password | N/A         | N/A            |

## Email Configuration

### Development

By default, emails are logged to `storage/logs/laravel.log`. No additional configuration needed!

### Testing with Mailtrap

For testing email delivery, use [Mailtrap](https://mailtrap.io/):

1. Sign up for a free account
2. Get your SMTP credentials
3. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

### Production

For production, use a professional email service (Mailgun, SendGrid, Amazon SES, etc.).

See [docs/MAIL_SETUP.md](docs/MAIL_SETUP.md) for detailed configuration instructions.

## Testing Email Notifications

Test all notification types:

```bash
php artisan notification:test all
```

Test specific notification:

```bash
php artisan notification:test assigned
php artisan notification:test overdue
php artisan notification:test completed
php artisan notification:test returned
```

## Database Schema

### User Fields

The `users` table includes the following fields:

| Field          | Type         | Description                                   | Required | Unique |
| -------------- | ------------ | --------------------------------------------- | -------- | ------ |
| id             | bigint       | Primary key                                   | Yes      | Yes    |
| name           | varchar(255) | Full name                                     | Yes      | No     |
| email          | varchar(255) | Email address                                 | Yes      | Yes    |
| password       | varchar(255) | Hashed password                               | Yes      | No     |
| role           | varchar(50)  | User role (requisitioner, sps, vp_acad, etc.) | Yes      | No     |
| department     | varchar(255) | Department or organizational unit             | Yes      | No     |
| school_year    | varchar(50)  | Academic year (e.g., "2024-2025", "1st Year") | No       | No     |
| student_number | varchar(50)  | Unique student identifier                     | No       | Yes    |
| is_active      | boolean      | Account active status                         | Yes      | No     |
| created_at     | timestamp    | Account creation timestamp                    | Yes      | No     |
| updated_at     | timestamp    | Last update timestamp                         | Yes      | No     |

### User Profile Management

Users can view and edit their academic information through:

-   **Profile Page**: `/profile` - View and edit school year and student number
-   **Admin Interface**: Administrators can manage all user fields including academic information

### Admin User Management

Administrators have additional capabilities:

-   View school year and student number in user list
-   Filter users by school year
-   Edit academic information for any user
-   Export user data including academic fields

## Workflow Stages

The system implements a 10-stage sequential approval process:

1. **SPS Review** (1 day) - School Principal/Supervisor
2. **VP Acad Review** (3 days) - Vice President for Academic Affairs
3. **Auditing Review** (3 days) - Auditor
4. **Senior VP Approval** (2 days) - Senior Vice President
5. **Acad Copy Distribution** (1 day) - VP Acad
6. **Auditing Copy Distribution** (1 day) - Auditor
7. **Voucher Preparation** (1 day) - Accounting
8. **Audit & Countersign** (1 day) - Auditor
9. **Cheque Preparation** (4 days) - Accounting
10. **Budget Release** (1 day) - Accounting

## Background Jobs and Scheduled Tasks

The system uses Laravel's queue system for asynchronous processing and scheduled tasks for automated monitoring.

### Queue Jobs

The following jobs are processed asynchronously:

1. **ConvertDocumentJob** - Converts Word documents to PDF for in-browser preview

    - Runs when a Word document is uploaded
    - Uses PHPWord and dompdf libraries
    - Implements retry logic with exponential backoff (3 attempts)

2. **SendDeadlineNotificationJob** - Sends email notifications when deadlines are reached

    - Dispatched by CheckDeadlinesJob
    - Notifies requisitioner and current stage assignee
    - Includes concept paper details and current status

3. **SendApprovalNotificationJob** - Sends email notifications when papers are fully approved

    - Dispatched when all workflow stages complete
    - Notifies requisitioner and administrators
    - Includes processing time summary

4. **CheckOverdueStages** - Identifies and notifies about overdue workflow stages

    - Scheduled to run hourly
    - Sends notifications for stages exceeding their max_days limit

5. **CheckDeadlinesJob** - Identifies concept papers that have reached their deadline
    - Scheduled to run hourly
    - Uses caching to ensure single notification per paper
    - Dispatches SendDeadlineNotificationJob for each reached deadline

### Scheduled Tasks

The system automatically schedules the following tasks (configured in `routes/console.php`):

```php
// Runs every hour
Schedule::job(new CheckOverdueStages)->hourly();
Schedule::job(new CheckDeadlinesJob)->hourly();
```

### Running Background Jobs

**Development:**

```bash
# Start queue worker
php artisan queue:work

# Start scheduler (for hourly tasks)
php artisan schedule:work

# Or use the combined dev script
composer dev
```

**Production:**

Configure Supervisor for queue workers and add cron job for scheduler (see Production Deployment section).

### Monitoring Jobs

```bash
# View queue status
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear all failed jobs
php artisan queue:flush
```

### Job Configuration

Queue configuration is in `config/queue.php`:

-   Default connection: `database` (suitable for small to medium applications)
-   Retry attempts: 3 with exponential backoff
-   Timeout: 90 seconds
-   For production with high volume, consider switching to Redis

## User Guide

The application includes a comprehensive in-app user guide accessible to all authenticated users.

### Accessing the User Guide

-   Click "User Guide" in the main navigation menu
-   Or visit `/user-guide` when logged in

### User Guide Sections

1. **Getting Started**: System overview, login process, and navigation
2. **Requisitioner Guide**: Submitting and tracking concept papers
3. **Approver Guide**: Reviewing, approving, and returning papers
4. **Administrator Guide**: User management, reports, and troubleshooting
5. **Workflow Process**: Complete 10-stage workflow documentation
6. **FAQ**: Frequently asked questions and support information

### Updating User Guide Content

User guide content is stored as Markdown files in `resources/docs/user-guide/`:

```
resources/docs/user-guide/
├── getting-started.md
├── requisitioner.md
├── approver.md
├── admin.md
├── workflow.md
└── faq.md
```

To update guide content:

1. **Edit Markdown files**: Modify the `.md` files in `resources/docs/user-guide/`
2. **Use standard Markdown**: Supports headings, lists, code blocks, tables, and links
3. **Add images**: Place images in `public/images/user-guide/` and reference with relative paths
4. **Test rendering**: View changes in the application after saving

### Adding New Guide Sections

To add a new section to the user guide:

1. **Create Markdown file**: Add new `.md` file in `resources/docs/user-guide/`
2. **Update controller**: Edit `app/Http/Controllers/UserGuideController.php`
    - Add section to `getSections()` method
    - Include title, icon, and subsections
3. **Add route** (if needed): Update `routes/web.php` for custom routes
4. **Update navigation**: Section will automatically appear in the table of contents

### Markdown Styling

The user guide uses Tailwind Typography plugin for consistent markdown rendering:

-   Headings are automatically styled with proper hierarchy
-   Code blocks include syntax highlighting
-   Tables are responsive and styled
-   Lists have proper spacing and indentation

### User Guide Content Update Procedures

Follow these steps to update user guide content:

1. **Locate the file**: Navigate to `resources/docs/user-guide/`
2. **Edit the Markdown file**: Open the relevant `.md` file in your editor
3. **Make changes**: Update content using standard Markdown syntax
4. **Add images** (if needed):
    - Place images in `public/images/user-guide/`
    - Reference in Markdown: `![Alt text](/images/user-guide/image.png)`
5. **Save the file**: Changes are immediately available (no build required)
6. **Test in browser**: Log in and navigate to the user guide to verify changes
7. **Commit changes**: Add to version control with descriptive commit message

**Best Practices:**

-   Keep content clear and concise
-   Use headings to organize content hierarchically
-   Include code examples where applicable
-   Add screenshots for complex UI interactions
-   Update the FAQ section with common user questions
-   Review content for accuracy after system updates

**Content Review Schedule:**

-   Review all guide sections quarterly
-   Update immediately after major feature releases
-   Incorporate user feedback and common support questions
-   Keep screenshots current with UI changes

## Documentation

-   [System Documentation](docs/SYSTEM_DOCUMENTATION.md) - Complete technical documentation including database schema, API endpoints, and architecture
-   [Dependencies Guide](docs/DEPENDENCIES.md) - Detailed information about external dependencies (PHPWord, Fabric.js, PDF.js) and their configuration
-   [Queue and Scheduler Setup](docs/QUEUE_AND_SCHEDULER_SETUP.md) - Comprehensive guide for setting up and managing background jobs and scheduled tasks
-   [Customization Guide](docs/CUSTOMIZATION.md) - Guide for customizing landing page, user registration, user guide, and styling
-   [Email Setup Guide](docs/MAIL_SETUP.md) - Detailed mail configuration instructions
-   [Email Notifications](docs/EMAIL_NOTIFICATIONS.md) - Complete notification system documentation
-   [User Guide](http://localhost:8000/user-guide) - In-app comprehensive user documentation (requires login)

## Development

### Running Tests

```bash
php artisan test
```

### Code Style

The project follows Laravel coding standards. Format code using:

```bash
./vendor/bin/pint
```

### Queue Management

Monitor queue:

```bash
php artisan queue:monitor
```

View failed jobs:

```bash
php artisan queue:failed
```

Retry failed jobs:

```bash
php artisan queue:retry all
```

## Production Deployment

### Environment Configuration

1. Set `APP_ENV=production` and `APP_DEBUG=false`
2. Configure production database
3. Set up SMTP email service
4. Configure queue worker with Supervisor
5. Set up scheduled tasks for overdue checking

### Queue Worker (Supervisor)

Create `/etc/supervisor/conf.d/concept-paper-tracker.conf`:

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

### Scheduled Tasks

Add to crontab:

```bash
* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1
```

## Troubleshooting

### Emails Not Sending

1. Ensure queue worker is running: `php artisan queue:work`
2. Check failed jobs: `php artisan queue:failed`
3. Verify mail configuration in `.env`
4. Check logs: `storage/logs/laravel.log`

### Queue Issues

1. Restart queue worker: `php artisan queue:restart`
2. Clear failed jobs: `php artisan queue:flush`
3. Check database connection

### Permission Issues

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

## License

This project is proprietary software. All rights reserved.

## Support

For issues and questions, please contact the development team.

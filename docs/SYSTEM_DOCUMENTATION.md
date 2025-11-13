# System Documentation

Complete technical documentation for the Concept Paper Tracker system.

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture](#architecture)
3. [Database Schema](#database-schema)
4. [User Management](#user-management)
5. [Authentication & Authorization](#authentication--authorization)
6. [Workflow System](#workflow-system)
7. [Notification System](#notification-system)
8. [File Management](#file-management)
9. [API Endpoints](#api-endpoints)
10. [Frontend Components](#frontend-components)

## System Overview

The Concept Paper Tracker is a web-based application designed to digitize and automate the approval workflow for concept papers with budget allocation. The system manages a 9-stage sequential approval process with role-based access control, automated notifications, and comprehensive audit trails.

### Technology Stack

-   **Backend Framework**: Laravel 12
-   **Frontend Framework**: React 18
-   **Frontend Bridge**: Inertia.js
-   **Styling**: Tailwind CSS 3
-   **Database**: SQLite (development) / MySQL/PostgreSQL (production)
-   **Queue System**: Laravel Queue (database driver)
-   **Email**: Laravel Mail with multiple driver support

### Key Features

-   Public landing page for system introduction
-   Enhanced user registration with academic information
-   Comprehensive in-app user guide
-   Role-based access control (6 roles)
-   9-stage sequential approval workflow
-   Automated email notifications
-   Deadline tracking and overdue alerts
-   Complete audit trail
-   File attachment support (PDF)
-   Admin dashboard with reporting
-   Responsive design

## Architecture

### Application Structure

```
concept-paper-tracker/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── AdminController.php
│   │   │   ├── ConceptPaperController.php
│   │   │   ├── DashboardController.php
│   │   │   ├── ProfileController.php
│   │   │   ├── UserGuideController.php
│   │   │   └── WorkflowStageController.php
│   │   ├── Middleware/
│   │   └── Requests/
│   ├── Models/
│   │   ├── User.php
│   │   ├── ConceptPaper.php
│   │   ├── WorkflowStage.php
│   │   └── Attachment.php
│   ├── Notifications/
│   │   ├── StageAssignedNotification.php
│   │   ├── StageOverdueNotification.php
│   │   ├── StageCompletedNotification.php
│   │   └── StageReturnedNotification.php
│   ├── Jobs/
│   │   └── CheckOverdueStages.php
│   └── Policies/
├── resources/
│   ├── js/
│   │   ├── Pages/
│   │   │   ├── Landing.jsx
│   │   │   ├── Auth/
│   │   │   ├── Dashboard.jsx
│   │   │   ├── ConceptPapers/
│   │   │   ├── Admin/
│   │   │   └── UserGuide/
│   │   ├── Components/
│   │   └── Layouts/
│   ├── views/
│   │   └── emails/
│   └── docs/
│       └── user-guide/
├── database/
│   ├── migrations/
│   └── seeders/
└── config/
    └── workflow.php
```

### Request Flow

```
User Request
    ↓
Route (web.php)
    ↓
Middleware (auth, role)
    ↓
Controller
    ↓
Model / Business Logic
    ↓
Inertia Response
    ↓
React Component
    ↓
User Interface
```

## Database Schema

### Users Table

Stores user account information including academic details.

```sql
CREATE TABLE users (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    department VARCHAR(255) NOT NULL,
    school_year VARCHAR(50) NULL,
    student_number VARCHAR(50) NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

**Field Descriptions:**

-   `id`: Unique identifier for the user
-   `name`: Full name of the user
-   `email`: Email address (used for login and notifications)
-   `email_verified_at`: Timestamp of email verification
-   `password`: Hashed password
-   `role`: User role (requisitioner, sps, vp_acad, auditor, accounting, admin)
-   `department`: Department or organizational unit
-   `school_year`: Academic year designation (e.g., "2024-2025", "1st Year")
-   `student_number`: Unique student identifier (optional, primarily for requisitioners)
-   `is_active`: Account active status
-   `remember_token`: Token for "remember me" functionality
-   `created_at`: Account creation timestamp
-   `updated_at`: Last update timestamp

**Indexes:**

-   Primary key on `id`
-   Unique index on `email`
-   Unique index on `student_number` (where not null)
-   Index on `role` for filtering
-   Index on `is_active` for filtering

### Concept Papers Table

Stores concept paper submissions.

```sql
CREATE TABLE concept_papers (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    budget_amount DECIMAL(10,2) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    current_stage_id BIGINT NULL,
    submitted_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (current_stage_id) REFERENCES workflow_stages(id) ON DELETE SET NULL
);
```

### Workflow Stages Table

Tracks the progress of concept papers through approval stages.

```sql
CREATE TABLE workflow_stages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    concept_paper_id BIGINT NOT NULL,
    stage_name VARCHAR(100) NOT NULL,
    assigned_role VARCHAR(50) NOT NULL,
    assigned_user_id BIGINT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    deadline TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    completed_by BIGINT NULL,
    remarks TEXT NULL,
    order INT NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (concept_paper_id) REFERENCES concept_papers(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL
);
```

### Attachments Table

Stores file attachments for concept papers.

```sql
CREATE TABLE attachments (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    concept_paper_id BIGINT NOT NULL,
    workflow_stage_id BIGINT NULL,
    uploaded_by BIGINT NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_size INT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (concept_paper_id) REFERENCES concept_papers(id) ON DELETE CASCADE,
    FOREIGN KEY (workflow_stage_id) REFERENCES workflow_stages(id) ON DELETE SET NULL,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);
```

### Audit Trail Table

Logs all actions performed on concept papers.

```sql
CREATE TABLE audit_trails (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    concept_paper_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT NULL,
    old_values JSON NULL,
    new_values JSON NULL,
    created_at TIMESTAMP NULL,
    FOREIGN KEY (concept_paper_id) REFERENCES concept_papers(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## User Management

### User Roles

The system supports six distinct user roles:

1. **Requisitioner**

    - Submit concept papers
    - Track submission status
    - View audit trail
    - Upload attachments
    - Academic fields: school_year, student_number

2. **SPS (School Principal/Supervisor)**

    - Review and approve concept papers at Stage 1
    - Add remarks and attachments
    - Return papers to requisitioner

3. **VP Acad (Vice President for Academic Affairs)**

    - Review at Stage 2
    - Distribute academic copies at Stage 4
    - Add remarks and attachments

4. **Auditor**

    - Review at Stage 3
    - Distribute audit copies at Stage 5
    - Countersign at Stage 7
    - Add remarks and attachments

5. **Accounting**

    - Prepare vouchers at Stage 6
    - Prepare cheques at Stage 8
    - Release budget at Stage 9
    - Add remarks and attachments

6. **Admin**
    - Full system access
    - User management (create, edit, deactivate)
    - View all concept papers
    - Generate reports
    - System configuration

### User Registration

#### Registration Process

1. User visits landing page
2. Clicks "Register" button
3. Fills registration form with:
    - Full name
    - Email address
    - Password (with confirmation)
    - Role selection
    - Department
    - School year (optional)
    - Student number (optional, shown for requisitioner role)
4. System validates input
5. Account created and user logged in
6. Redirected to dashboard

#### Validation Rules

```php
[
    'name' => ['required', 'string', 'max:255'],
    'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    'password' => ['required', 'confirmed', Rules\Password::defaults()],
    'role' => ['required', 'in:requisitioner,sps,vp_acad,auditor,accounting'],
    'department' => ['required', 'string', 'max:255'],
    'school_year' => ['nullable', 'string', 'max:50'],
    'student_number' => ['nullable', 'string', 'max:50', 'unique:users'],
]
```

### User Profile Management

Users can view and edit their profile information:

**Editable Fields:**

-   Name
-   Email
-   Password
-   Department
-   School year
-   Student number

**View-Only Fields:**

-   Role (can only be changed by admin)
-   Account creation date
-   Last update date

### Admin User Management

Administrators have access to comprehensive user management:

**Capabilities:**

-   View all users with pagination
-   Filter users by:
    -   Role
    -   Department
    -   School year
    -   Active status
-   Search users by name or email
-   Create new users
-   Edit user information (including role)
-   Deactivate/activate user accounts
-   Export user data to CSV/Excel

**User List Columns:**

-   Name
-   Email
-   Role
-   Department
-   School Year
-   Student Number
-   Active Status
-   Actions (Edit, Deactivate)

## Authentication & Authorization

### Authentication

The system uses Laravel's built-in authentication with session-based login.

**Login Process:**

1. User enters email and password
2. System validates credentials
3. Session created
4. User redirected to dashboard

**Password Requirements:**

-   Minimum 8 characters
-   At least one uppercase letter
-   At least one lowercase letter
-   At least one number

### Authorization

Role-based access control is implemented using Laravel policies and middleware.

**Middleware:**

-   `auth`: Requires authenticated user
-   `role:admin`: Requires admin role
-   `role:requisitioner,sps`: Requires one of specified roles

**Policy Examples:**

```php
// ConceptPaperPolicy
public function view(User $user, ConceptPaper $paper)
{
    return $user->id === $paper->user_id
        || $user->role === 'admin'
        || $this->canAccessCurrentStage($user, $paper);
}

public function update(User $user, ConceptPaper $paper)
{
    return $user->id === $paper->user_id
        && $paper->status === 'draft';
}
```

## Workflow System

### Workflow Stages

The system implements a 9-stage sequential approval process:

| Stage | Name                       | Role       | Duration | Description                         |
| ----- | -------------------------- | ---------- | -------- | ----------------------------------- |
| 1     | SPS Review                 | sps        | 1 day    | Initial review by SPS               |
| 2     | VP Acad Review             | vp_acad    | 3 days   | Academic review                     |
| 3     | Auditing Review            | auditor    | 3 days   | Audit review                        |
| 4     | Acad Copy Distribution     | vp_acad    | 1 day    | Distribute academic copies          |
| 5     | Auditing Copy Distribution | auditor    | 1 day    | Distribute audit copies             |
| 6     | Voucher Preparation        | accounting | 1 day    | Prepare payment voucher             |
| 7     | Audit & Countersign        | auditor    | 1 day    | Countersign voucher                 |
| 8     | Cheque Preparation         | accounting | 4 days   | Prepare cheque                      |
| 9     | Budget Release             | accounting | 1 day    | Release budget and complete process |

### Stage Lifecycle

```
Created → Pending → In Progress → Completed
                  ↓
                Returned (back to requisitioner)
```

**Stage States:**

-   `pending`: Waiting for assigned user to start
-   `in_progress`: User has started working on stage
-   `completed`: Stage finished, moves to next stage
-   `returned`: Sent back to requisitioner for revisions
-   `overdue`: Deadline passed without completion

### Workflow Actions

**Complete Stage:**

```php
POST /workflow-stages/{stage}/complete
Body: { remarks: "Optional remarks" }
```

**Return Stage:**

```php
POST /workflow-stages/{stage}/return
Body: { remarks: "Required remarks explaining return" }
```

**Add Attachment:**

```php
POST /workflow-stages/{stage}/attachments
Body: { file: File }
```

## Notification System

### Notification Types

1. **Stage Assigned**

    - Sent when a stage is assigned to a user
    - Includes paper details and deadline
    - Action button to view paper

2. **Stage Overdue**

    - Sent when deadline passes without completion
    - Daily reminder until completed
    - Includes days overdue

3. **Stage Completed**

    - Sent to requisitioner when stage completed
    - Shows progress through workflow
    - Includes completer's remarks

4. **Stage Returned**
    - Sent to requisitioner when paper returned
    - Includes return remarks
    - Action button to revise and resubmit

### Notification Channels

-   **Email**: Primary notification channel
-   **Database**: In-app notification bell
-   **Queue**: Asynchronous processing

### Notification Configuration

```php
// config/mail.php
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'noreply@example.com'),
    'name' => env('MAIL_FROM_NAME', 'Concept Paper Tracker'),
],
```

## File Management

### Supported File Types

-   PDF documents only
-   Maximum file size: 10MB (configurable)

### Storage Structure

```
storage/app/
└── concept-papers/
    └── {concept_paper_id}/
        ├── original.pdf
        └── stage-{stage_id}-{filename}.pdf
```

### File Upload Process

1. User selects PDF file
2. Client-side validation (type, size)
3. File uploaded to server
4. Server-side validation
5. File stored in storage directory
6. Database record created
7. Success response returned

### File Security

-   Files stored outside public directory
-   Access controlled through controller
-   Authorization check before serving file
-   Temporary signed URLs for downloads

## API Endpoints

### Authentication

```
POST   /register          - Register new user
POST   /login             - Login user
POST   /logout            - Logout user
POST   /forgot-password   - Request password reset
POST   /reset-password    - Reset password
```

### Concept Papers

```
GET    /concept-papers              - List user's papers
POST   /concept-papers              - Create new paper
GET    /concept-papers/{id}         - View paper details
PUT    /concept-papers/{id}         - Update paper (draft only)
DELETE /concept-papers/{id}         - Delete paper (draft only)
POST   /concept-papers/{id}/submit  - Submit paper for approval
```

### Workflow Stages

```
GET    /workflow-stages/{id}           - View stage details
POST   /workflow-stages/{id}/complete  - Complete stage
POST   /workflow-stages/{id}/return    - Return stage
POST   /workflow-stages/{id}/start     - Start working on stage
```

### Attachments

```
POST   /attachments                    - Upload attachment
GET    /attachments/{id}               - Download attachment
DELETE /attachments/{id}               - Delete attachment
```

### Admin

```
GET    /admin/users                    - List all users
POST   /admin/users                    - Create user
PUT    /admin/users/{id}               - Update user
DELETE /admin/users/{id}               - Deactivate user
GET    /admin/reports                  - Generate reports
GET    /admin/dashboard                - Admin dashboard data
```

### User Guide

```
GET    /user-guide                     - User guide index
GET    /user-guide/{section}           - View guide section
```

### Profile

```
GET    /profile                        - View profile
PUT    /profile                        - Update profile
DELETE /profile                        - Delete account
```

## Frontend Components

### Page Components

**Landing Page:**

-   `Landing.jsx` - Main landing page
-   `Landing/HeroSection.jsx` - Hero banner
-   `Landing/FeaturesSection.jsx` - Features grid
-   `Landing/WorkflowSection.jsx` - Workflow visualization
-   `Landing/RolesSection.jsx` - User roles
-   `Landing/UseCasesSection.jsx` - Use cases
-   `Landing/CTASection.jsx` - Call to action
-   `Landing/Footer.jsx` - Footer

**Authentication:**

-   `Auth/Login.jsx` - Login page
-   `Auth/Register.jsx` - Registration page
-   `Auth/ForgotPassword.jsx` - Password reset request
-   `Auth/ResetPassword.jsx` - Password reset form

**Dashboard:**

-   `Dashboard.jsx` - Main dashboard

**Concept Papers:**

-   `ConceptPapers/Index.jsx` - List papers
-   `ConceptPapers/Create.jsx` - Create paper
-   `ConceptPapers/Show.jsx` - View paper details
-   `ConceptPapers/Edit.jsx` - Edit paper

**Admin:**

-   `Admin/Dashboard.jsx` - Admin dashboard
-   `Admin/UserManagement.jsx` - User management
-   `Admin/Reports.jsx` - Reports

**User Guide:**

-   `UserGuide/Index.jsx` - Guide index
-   `UserGuide/Section.jsx` - Guide section viewer

**Profile:**

-   `Profile/Edit.jsx` - Edit profile
-   `Profile/Partials/UpdateProfileInformation.jsx`
-   `Profile/Partials/UpdatePassword.jsx`
-   `Profile/Partials/DeleteAccount.jsx`

### Reusable Components

**Form Components:**

-   `TextInput.jsx` - Text input field
-   `InputLabel.jsx` - Form label
-   `InputError.jsx` - Error message
-   `PrimaryButton.jsx` - Primary action button
-   `SecondaryButton.jsx` - Secondary action button
-   `DangerButton.jsx` - Destructive action button

**Layout Components:**

-   `AuthenticatedLayout.jsx` - Layout for authenticated pages
-   `GuestLayout.jsx` - Layout for guest pages

**UI Components:**

-   `Modal.jsx` - Modal dialog
-   `Dropdown.jsx` - Dropdown menu
-   `Pagination.jsx` - Pagination controls
-   `Badge.jsx` - Status badge
-   `Card.jsx` - Card container

## Configuration

### Environment Variables

```env
# Application
APP_NAME="Concept Paper Tracker"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

# Mail
MAIL_MAILER=log
MAIL_FROM_ADDRESS=noreply@example.com
MAIL_FROM_NAME="${APP_NAME}"

# Queue
QUEUE_CONNECTION=database
```

### Workflow Configuration

Edit `config/workflow.php` to customize workflow stages:

```php
return [
    'stages' => [
        [
            'name' => 'SPS Review',
            'role' => 'sps',
            'duration_days' => 1,
            'description' => 'Initial review by School Principal/Supervisor',
            'order' => 1,
        ],
        // ... more stages
    ],
];
```

## Maintenance

### Database Backup

```bash
# SQLite
cp database/database.sqlite database/backups/database-$(date +%Y%m%d).sqlite

# MySQL
mysqldump -u username -p database_name > backup-$(date +%Y%m%d).sql
```

### Log Management

```bash
# View logs
tail -f storage/logs/laravel.log

# Clear old logs
php artisan log:clear
```

### Cache Management

```bash
# Clear all caches
php artisan optimize:clear

# Rebuild caches
php artisan optimize
```

### Queue Management

```bash
# Monitor queue
php artisan queue:monitor

# View failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

## Security Considerations

### Password Security

-   Passwords hashed using bcrypt
-   Minimum password requirements enforced
-   Password reset via email token

### File Upload Security

-   File type validation (PDF only)
-   File size limits enforced
-   Files stored outside public directory
-   Authorization required for file access

### SQL Injection Prevention

-   Eloquent ORM with parameter binding
-   Input validation on all requests
-   Prepared statements for raw queries

### XSS Prevention

-   React automatically escapes output
-   Blade templates escape by default
-   Content Security Policy headers

### CSRF Protection

-   CSRF tokens on all forms
-   Token validation on POST requests
-   Inertia.js handles token automatically

## Performance Optimization

### Database Optimization

-   Indexes on frequently queried columns
-   Eager loading to prevent N+1 queries
-   Query result caching where appropriate

### Frontend Optimization

-   Code splitting with Vite
-   Lazy loading of components
-   Image optimization
-   Minification of assets

### Caching Strategy

-   Route caching: `php artisan route:cache`
-   Config caching: `php artisan config:cache`
-   View caching: `php artisan view:cache`
-   Query result caching for reports

## Troubleshooting

### Common Issues

**Issue: Emails not sending**

-   Check queue worker is running
-   Verify mail configuration
-   Check failed jobs queue
-   Review logs

**Issue: File upload fails**

-   Check storage permissions
-   Verify file size limits
-   Check disk space
-   Review upload configuration

**Issue: Slow performance**

-   Enable query logging to find slow queries
-   Check for N+1 query problems
-   Review database indexes
-   Enable caching

**Issue: Authentication errors**

-   Clear session data
-   Regenerate application key
-   Check database connection
-   Verify user credentials

## Support and Resources

### Documentation

-   Laravel: https://laravel.com/docs
-   React: https://react.dev
-   Inertia.js: https://inertiajs.com
-   Tailwind CSS: https://tailwindcss.com/docs

### Development Team

For technical support and questions, contact the development team.

# Workflow Enhancements Documentation

This document provides comprehensive documentation for the workflow enhancements feature, including user activation management, Senior VP approval stage, document preview and annotation, deadline management, and conditional workflow routing.

## Table of Contents

1. [Overview](#overview)
2. [User Activation Management](#user-activation-management)
3. [Senior VP Approval Stage](#senior-vp-approval-stage)
4. [Document Preview and Annotation](#document-preview-and-annotation)
5. [Deadline Management](#deadline-management)
6. [Conditional Workflow Routing](#conditional-workflow-routing)
7. [API Reference](#api-reference)
8. [Configuration](#configuration)

## Overview

The workflow enhancements extend the Concept Paper Tracker with advanced capabilities:

-   **User Activation Controls**: Administrators can activate/deactivate users and reassign their workflow stages
-   **Senior VP Approval**: New approval stage between Auditing Review and Acad Copy Distribution
-   **Document Preview**: In-browser preview of PDF and Word documents with annotation tools
-   **Flexible Deadlines**: User-selectable deadlines with automated notifications
-   **Smart Routing**: Automatic skipping of SPS stage for papers without student involvement

### Key Benefits

-   **Improved Flexibility**: Administrators can manage user availability without losing workflow continuity
-   **Enhanced Oversight**: Senior VP approval adds high-level review to the process
-   **Better Collaboration**: Annotation tools enable visual feedback on documents
-   **Clearer Expectations**: Deadline selection and notifications keep stakeholders informed
-   **Streamlined Process**: Conditional routing eliminates unnecessary approval steps

## User Activation Management

### Overview

Administrators can activate or deactivate user accounts to control system access. When a user is deactivated, their pending workflow stages must be reassigned to maintain process continuity.

### Activation Status

Each user account has an activation status:

-   **Active**: User can log in and receive stage assignments
-   **Inactive**: User cannot log in and cannot receive new assignments

### Deactivating a User

**Steps:**

1. Navigate to Admin → User Management
2. Locate the user in the list
3. Click the "Deactivate" button
4. Review the list of affected concept papers (if any)
5. Reassign pending stages to active users
6. Confirm deactivation

**What Happens:**

-   User's `is_active` status set to `false`
-   `deactivated_at` timestamp recorded
-   `deactivated_by` records the administrator's ID
-   User cannot log in
-   User cannot receive new stage assignments
-   Existing stage assignments must be reassigned

### Reassigning Workflow Stages

When deactivating a user with pending stages:

**Automatic Detection:**

-   System identifies all workflow stages assigned to the user
-   Displays list of affected concept papers with stage details

**Reassignment Process:**

1. For each affected stage:

    - Select a new assignee from dropdown
    - Only active users with matching role are shown
    - Optionally add reassignment notes

2. Bulk reassignment option:

    - Select one user to receive all stages
    - Useful when reassigning to a backup approver

3. Confirm reassignment:
    - All stages reassigned atomically
    - Audit trail records each reassignment
    - Notifications sent to new assignees

**Audit Trail:**

Each reassignment creates an audit log entry with:

-   Administrator who performed reassignment
-   Original assignee
-   New assignee
-   Timestamp
-   Reassignment reason (if provided)

### Reactivating a User

**Steps:**

1. Navigate to Admin → User Management
2. Filter by "Inactive" users
3. Locate the user
4. Click "Activate" button
5. User can immediately log in and receive assignments

**What Happens:**

-   User's `is_active` status set to `true`
-   `deactivated_at` and `deactivated_by` cleared
-   User can log in
-   User can receive stage assignments

### Viewing Activation History

**User List Columns:**

-   Activation status badge (Active/Inactive)
-   Deactivation date (if inactive)
-   Deactivated by (administrator name)

**Filtering:**

-   Filter by: All Users, Active Only, Inactive Only
-   Sort by activation status
-   Search includes activation status

### Permissions

-   Only administrators can activate/deactivate users
-   Only administrators can reassign workflow stages
-   Users cannot deactivate themselves
-   At least one admin must remain active

### Best Practices

1. **Plan Ahead**: Deactivate users during low-activity periods
2. **Communicate**: Inform affected users before deactivation
3. **Reassign Promptly**: Don't leave stages unassigned
4. **Document Reasons**: Add notes explaining deactivation
5. **Review Regularly**: Audit inactive users quarterly

## Senior VP Approval Stage

### Overview

The Senior VP Approval stage provides high-level oversight between Auditing Review and Acad Copy Distribution. This ensures senior leadership reviews all concept papers before final distribution.

### Stage Details

**Position**: Stage 4 (between Auditing Review and Acad Copy Distribution)

**Role**: Senior VP (senior_vp)

**Duration**: 2 days

**Description**: Senior Vice President reviews and approves concept paper after audit review

### Updated Workflow Sequence

The complete 10-stage workflow:

1. SPS Review (1 day) - sps
2. VP Acad Review (3 days) - vp_acad
3. Auditing Review (3 days) - auditor
4. **Senior VP Approval (2 days) - senior_vp** ← NEW
5. Acad Copy Distribution (1 day) - vp_acad
6. Auditing Copy Distribution (1 day) - auditor
7. Voucher Preparation (1 day) - accounting
8. Audit & Countersign (1 day) - auditor
9. Cheque Preparation (4 days) - accounting
10. Budget Release (1 day) - accounting

### Senior VP Role

**Responsibilities:**

-   Review concept papers after auditing
-   Verify budget allocations align with strategic priorities
-   Approve or return papers for revision
-   Add remarks and recommendations
-   Upload supporting documentation

**Permissions:**

-   View all concept papers at Senior VP Approval stage
-   Complete or return Senior VP Approval stage
-   Add remarks and attachments
-   View complete audit trail

### Creating Senior VP Users

**Via Admin Interface:**

1. Navigate to Admin → User Management
2. Click "Create User"
3. Fill in user details
4. Select role: "Senior VP"
5. Save user

**Via Registration:**

Senior VP role cannot be self-selected during registration. Only administrators can assign this role.

### Stage Actions

**Approve:**

```
POST /workflow-stages/{id}/complete
Body: { remarks: "Approved for distribution" }
```

**Return for Revision:**

```
POST /workflow-stages/{id}/return
Body: { remarks: "Please revise budget justification" }
```

**Add Attachment:**

```
POST /attachments
Body: {
  concept_paper_id: 123,
  workflow_stage_id: 456,
  file: File
}
```

### Notifications

**Stage Assigned:**

-   Sent when paper reaches Senior VP Approval
-   Includes paper details and 2-day deadline
-   Email and in-app notification

**Stage Overdue:**

-   Sent if not completed within 2 days
-   Daily reminders until completed

**Stage Completed:**

-   Sent to requisitioner when approved
-   Includes Senior VP remarks

### Migration Impact

**Existing Papers:**

Papers in progress when the feature is deployed:

-   Papers before Auditing Review: Will include Senior VP stage
-   Papers at Auditing Review: Senior VP stage added upon completion
-   Papers after Auditing Review: Senior VP stage inserted retroactively

**Data Migration:**

The migration script automatically:

-   Adds Senior VP stage to in-progress papers
-   Adjusts stage order numbers
-   Preserves existing stage data

## Document Preview and Annotation

### Overview

Users can preview uploaded documents directly in the browser and add annotations including markers, highlights, and discrepancy flags. This enables visual feedback without downloading files.

### Supported File Types

**PDF Documents:**

-   Native browser preview
-   No conversion required
-   Immediate rendering

**Word Documents:**

-   .doc (Microsoft Word 97-2003)
-   .docx (Microsoft Word 2007+)
-   Converted to PDF for preview
-   Original format preserved for download

### Document Preview Features

**Viewing Controls:**

-   Page navigation (previous/next, jump to page)
-   Zoom in/out (50% to 200%)
-   Fit to width/height
-   Full-screen mode
-   Rotate pages

**Performance:**

-   Lazy loading of pages
-   Cached conversions (24 hours)
-   Progressive rendering for large documents
-   Background conversion for Word documents

### Annotation Tools

**Marker Tool:**

-   Click to place marker icon
-   Add text comment
-   Choose marker color
-   Drag to reposition

**Highlight Tool:**

-   Click and drag to highlight text/area
-   Choose highlight color
-   Add text comment
-   Adjust highlight bounds

**Drawing Tool:**

-   Freehand drawing on document
-   Choose pen color and width
-   Add text comment
-   Erase or clear drawings

**Discrepancy Marker:**

-   Special marker for issues/errors
-   Requires text comment (mandatory)
-   Visually distinct (red color, warning icon)
-   Appears in discrepancy summary

### Creating Annotations

**Steps:**

1. Open document preview
2. Select annotation tool from toolbar
3. Click or drag on document to create annotation
4. Add text comment (required for discrepancies)
5. Click "Save" to persist annotation

**Annotation Data:**

Each annotation includes:

-   Type (marker, highlight, drawing, discrepancy)
-   Page number
-   Coordinates (x, y, width, height)
-   Text comment
-   Creator (user ID and name)
-   Timestamp
-   Discrepancy flag

### Viewing Annotations

**All Users See Annotations:**

-   Annotations visible to everyone with paper access
-   Creator name and timestamp displayed
-   Hover to view full comment
-   Click to edit (own annotations only)

**Annotation List:**

-   Sidebar shows all annotations for current page
-   Filter by type or creator
-   Jump to annotation location
-   Sort by date or page

### Discrepancy Summary

**Overview:**

-   Dedicated section showing all discrepancies
-   Grouped by document and page
-   Includes creator, timestamp, and comment
-   Click to jump to discrepancy in preview

**Accessing:**

-   "Discrepancies" tab on concept paper detail page
-   Shows count badge (e.g., "3 discrepancies")
-   Export discrepancies to PDF report

**Discrepancy Details:**

Each entry shows:

-   Document name
-   Page number
-   Discrepancy comment
-   Created by (user name)
-   Created at (timestamp)
-   Link to view in context

### Mobile Support

**Touch Gestures:**

-   Pinch to zoom
-   Swipe to navigate pages
-   Tap to place markers
-   Long press for context menu

**Responsive Interface:**

-   Annotation tools adapt to screen size
-   Simplified toolbar on mobile
-   Touch-friendly controls
-   Optimized for tablets

### Permissions

**View Annotations:**

-   All users with paper access can view annotations
-   Annotations visible across all workflow stages

**Create Annotations:**

-   Users with paper access can create annotations
-   Available at any workflow stage

**Edit Annotations:**

-   Users can edit their own annotations
-   Administrators can edit any annotation

**Delete Annotations:**

-   Users can delete their own annotations
-   Administrators can delete any annotation

### Best Practices

1. **Use Discrepancy Markers**: Flag issues that require action
2. **Add Clear Comments**: Explain what needs attention
3. **Review Before Submitting**: Check all discrepancies addressed
4. **Use Appropriate Tools**: Markers for points, highlights for sections
5. **Coordinate with Team**: Discuss annotations before returning paper

## Deadline Management

### Overview

Requisitioners select a deadline when submitting concept papers. The system calculates the absolute deadline date and sends notifications when deadlines are reached.

### Deadline Options

**Predefined Options:**

-   1 Week (7 days)
-   2 Weeks (14 days)
-   1 Month (30 days)
-   2 Months (60 days)
-   3 Months (90 days)

**Selection:**

-   Required field on submission form
-   Displayed as dropdown/combo box
-   Shows calculated date after selection
-   Cannot be changed after submission

### Deadline Calculation

**Formula:**

```
Deadline Date = Submission Date + Selected Timeframe
```

**Example:**

-   Submission: January 15, 2025
-   Selected: 1 Month
-   Deadline: February 14, 2025

**Time:**

-   Deadline set to end of business day (5:00 PM)
-   Timezone: System default timezone
-   Excludes weekends and holidays (optional configuration)

### Deadline Display

**Concept Paper List:**

-   Deadline date column
-   Visual indicator for approaching deadlines
-   Red badge for reached/overdue deadlines

**Concept Paper Details:**

-   Prominent deadline display
-   Days remaining counter
-   Progress bar showing time elapsed

**Dashboard:**

-   "Approaching Deadlines" widget
-   Shows papers due within 7 days
-   Sorted by deadline date

### Deadline Notifications

**When Deadline Reached:**

Automatic email sent to:

-   Requisitioner who submitted the paper
-   User assigned to current workflow stage
-   Administrators (optional)

**Notification Content:**

-   Concept paper title
-   Tracking number
-   Current workflow stage
-   Deadline date
-   Days overdue (if applicable)
-   Link to view paper

**Notification Timing:**

-   Sent within 1 hour of deadline
-   Single notification (not repeated)
-   Cached to prevent duplicates

**Background Job:**

```php
// Runs hourly via scheduler
CheckDeadlinesJob::dispatch();

// Checks for papers where:
// - deadline_date <= now()
// - status != 'completed'
// - notification not already sent
```

### Configuring Deadline Options

**Admin Interface:**

1. Navigate to Admin → Settings → Deadline Options
2. View current options
3. Add new option:
    - Label (e.g., "6 Months")
    - Days (e.g., 180)
4. Edit existing option
5. Delete option (if not in use)

**Configuration File:**

Edit `config/workflow.php`:

```php
'deadline_options' => [
    '1_week' => ['label' => '1 Week', 'days' => 7],
    '2_weeks' => ['label' => '2 Weeks', 'days' => 14],
    '1_month' => ['label' => '1 Month', 'days' => 30],
    '2_months' => ['label' => '2 Months', 'days' => 60],
    '3_months' => ['label' => '3 Months', 'days' => 90],
    // Add custom options here
],
```

**Validation:**

-   Days must be positive integer
-   Label must be unique
-   Cannot delete option in use by existing papers

### Deadline vs. Stage Deadlines

**Paper Deadline:**

-   Overall completion deadline
-   Selected by requisitioner
-   Applies to entire workflow
-   Notification when reached

**Stage Deadlines:**

-   Individual stage time limits
-   Defined in workflow configuration
-   Automatic calculation per stage
-   Overdue notifications per stage

**Both Are Independent:**

-   Paper can be overdue but stages on time
-   Stages can be overdue but paper deadline not reached
-   Both tracked and monitored separately

### Best Practices

1. **Choose Realistic Deadlines**: Consider workflow complexity
2. **Communicate Urgency**: Shorter deadlines for urgent papers
3. **Monitor Progress**: Check dashboard for approaching deadlines
4. **Respond to Notifications**: Act promptly when deadline reached
5. **Plan for Delays**: Build buffer time into deadline selection

## Conditional Workflow Routing

### Overview

The system automatically skips the SPS Review stage for concept papers that don't involve students. This streamlines the approval process for non-student papers.

### Student Involvement Field

**Location**: Concept paper submission form

**Options**:

-   Yes - Students are involved
-   No - No student involvement

**Required**: Must be selected before submission

**Cannot Change**: Fixed after submission

### Workflow Routing Logic

**Students Involved = Yes:**

Standard workflow (all 10 stages):

1. SPS Review
2. VP Acad Review
3. Auditing Review
4. Senior VP Approval
5. Acad Copy Distribution
6. Auditing Copy Distribution
7. Voucher Preparation
8. Audit & Countersign
9. Cheque Preparation
10. Budget Release

**Students Involved = No:**

SPS stage skipped (9 stages):

1. ~~SPS Review~~ ← SKIPPED
2. VP Acad Review ← FIRST STAGE
3. Auditing Review
4. Senior VP Approval
5. Acad Copy Distribution
6. Auditing Copy Distribution
7. Voucher Preparation
8. Audit & Countersign
9. Cheque Preparation
10. Budget Release

### Implementation Details

**Stage Creation:**

```php
// WorkflowService::createStagesForPaper()
if (!$paper->students_involved && $stage->name === 'SPS Review') {
    // Skip SPS stage
    continue;
}
// Create other stages normally
```

**Audit Trail:**

When SPS stage skipped:

-   Audit log entry created
-   Reason: "SPS Review skipped - no student involvement"
-   Timestamp recorded
-   Visible in audit trail

### Visual Indicators

**Concept Paper List:**

-   "Students Involved" column with Yes/No badge
-   Filter by student involvement status
-   Sort by student involvement

**Concept Paper Details:**

-   Student involvement badge (prominent)
-   Workflow visualization shows skipped stage (grayed out)
-   Audit trail explains skip reason

**Workflow Timeline:**

-   Skipped stages shown with strikethrough
-   Tooltip explains why skipped
-   Timeline adjusts to show actual stages

### Filtering and Reporting

**Filter Options:**

-   All Papers
-   With Student Involvement
-   Without Student Involvement

**Reports:**

-   Include student involvement column
-   Separate statistics for each type
-   Average processing time by type

### Permissions

**Set Student Involvement:**

-   Only requisitioner during submission
-   Cannot be changed after submission
-   Administrators cannot override

**View Student Involvement:**

-   All users with paper access
-   Visible in list and detail views
-   Included in notifications

### Best Practices

1. **Accurate Selection**: Carefully determine student involvement
2. **Consistent Criteria**: Use same definition across organization
3. **Document Policy**: Define what constitutes "student involvement"
4. **Review Regularly**: Audit papers to ensure correct classification
5. **Train Users**: Educate requisitioners on proper selection

## API Reference

### User Activation Endpoints

**Toggle User Activation**

```http
PATCH /api/admin/users/{id}/activation
Authorization: Bearer {token}
Content-Type: application/json

{
  "is_active": false,
  "reason": "User on extended leave"
}
```

Response:

```json
{
    "success": true,
    "user": {
        "id": 123,
        "name": "John Doe",
        "is_active": false,
        "deactivated_at": "2025-01-15T10:30:00Z",
        "deactivated_by": 1
    },
    "affected_stages": [
        {
            "id": 456,
            "concept_paper_id": 789,
            "stage_name": "VP Acad Review",
            "paper_title": "Research Project"
        }
    ]
}
```

**Get User's Assigned Stages**

```http
GET /api/admin/users/{id}/assigned-stages
Authorization: Bearer {token}
```

Response:

```json
{
    "stages": [
        {
            "id": 456,
            "concept_paper_id": 789,
            "stage_name": "VP Acad Review",
            "status": "pending",
            "deadline": "2025-01-20T17:00:00Z",
            "paper": {
                "id": 789,
                "title": "Research Project",
                "tracking_number": "CP-2025-001"
            }
        }
    ]
}
```

**Reassign Workflow Stage**

```http
POST /api/admin/stages/{id}/reassign
Authorization: Bearer {token}
Content-Type: application/json

{
  "new_user_id": 124,
  "reason": "Original assignee unavailable"
}
```

Response:

```json
{
    "success": true,
    "stage": {
        "id": 456,
        "assigned_user_id": 124,
        "reassigned_at": "2025-01-15T10:35:00Z",
        "reassigned_by": 1
    }
}
```

### Annotation Endpoints

**Create Annotation**

```http
POST /api/annotations
Authorization: Bearer {token}
Content-Type: application/json

{
  "concept_paper_id": 789,
  "attachment_id": 101,
  "page_number": 3,
  "annotation_type": "discrepancy",
  "coordinates": {
    "x": 150,
    "y": 200,
    "width": 100,
    "height": 50
  },
  "comment": "Budget calculation error",
  "is_discrepancy": true
}
```

Response:

```json
{
    "id": 555,
    "concept_paper_id": 789,
    "attachment_id": 101,
    "user_id": 123,
    "page_number": 3,
    "annotation_type": "discrepancy",
    "coordinates": {
        "x": 150,
        "y": 200,
        "width": 100,
        "height": 50
    },
    "comment": "Budget calculation error",
    "is_discrepancy": true,
    "created_at": "2025-01-15T10:40:00Z",
    "user": {
        "id": 123,
        "name": "Jane Smith"
    }
}
```

**Get Annotations for Document**

```http
GET /api/concept-papers/{id}/annotations?attachment_id=101&page_number=3
Authorization: Bearer {token}
```

Response:

```json
{
  "annotations": [
    {
      "id": 555,
      "page_number": 3,
      "annotation_type": "discrepancy",
      "coordinates": {...},
      "comment": "Budget calculation error",
      "is_discrepancy": true,
      "created_at": "2025-01-15T10:40:00Z",
      "user": {
        "id": 123,
        "name": "Jane Smith"
      }
    }
  ]
}
```

**Get Discrepancy Summary**

```http
GET /api/concept-papers/{id}/discrepancies
Authorization: Bearer {token}
```

Response:

```json
{
    "discrepancies": [
        {
            "id": 555,
            "attachment": {
                "id": 101,
                "file_name": "budget.pdf"
            },
            "page_number": 3,
            "comment": "Budget calculation error",
            "created_at": "2025-01-15T10:40:00Z",
            "user": {
                "id": 123,
                "name": "Jane Smith"
            }
        }
    ],
    "total_count": 1
}
```

**Update Annotation**

```http
PUT /api/annotations/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "comment": "Updated: Budget calculation needs revision",
  "coordinates": {
    "x": 150,
    "y": 200,
    "width": 120,
    "height": 60
  }
}
```

**Delete Annotation**

```http
DELETE /api/annotations/{id}
Authorization: Bearer {token}
```

### Document Preview Endpoints

**Get Document Preview**

```http
GET /api/attachments/{id}/preview
Authorization: Bearer {token}
```

Response:

-   Content-Type: application/pdf
-   Binary PDF data (converted if Word document)
-   Cache-Control headers for 24-hour caching

**Download Original Document**

```http
GET /api/attachments/{id}/download
Authorization: Bearer {token}
```

Response:

-   Content-Type: original MIME type
-   Content-Disposition: attachment
-   Binary file data

### Deadline Management Endpoints

**Get Deadline Options**

```http
GET /api/deadline-options
Authorization: Bearer {token}
```

Response:

```json
{
    "options": [
        {
            "key": "1_week",
            "label": "1 Week",
            "days": 7
        },
        {
            "key": "2_weeks",
            "label": "2 Weeks",
            "days": 14
        }
    ]
}
```

**Create/Update Deadline Option (Admin)**

```http
POST /api/admin/deadline-options
Authorization: Bearer {token}
Content-Type: application/json

{
  "key": "6_months",
  "label": "6 Months",
  "days": 180
}
```

**Delete Deadline Option (Admin)**

```http
DELETE /api/admin/deadline-options/{key}
Authorization: Bearer {token}
```

## Configuration

### Workflow Configuration

Edit `config/workflow.php`:

```php
return [
    'stages' => [
        [
            'name' => 'SPS Review',
            'role' => 'sps',
            'duration_days' => 1,
            'skippable' => true, // Can be skipped based on conditions
            'order' => 1,
        ],
        [
            'name' => 'VP Acad Review',
            'role' => 'vp_acad',
            'duration_days' => 3,
            'order' => 2,
        ],
        [
            'name' => 'Auditing Review',
            'role' => 'auditor',
            'duration_days' => 3,
            'order' => 3,
        ],
        [
            'name' => 'Senior VP Approval',
            'role' => 'senior_vp',
            'duration_days' => 2,
            'order' => 4,
        ],
        // ... remaining stages
    ],

    'deadline_options' => [
        '1_week' => ['label' => '1 Week', 'days' => 7],
        '2_weeks' => ['label' => '2 Weeks', 'days' => 14],
        '1_month' => ['label' => '1 Month', 'days' => 30],
        '2_months' => ['label' => '2 Months', 'days' => 60],
        '3_months' => ['label' => '3 Months', 'days' => 90],
    ],

    'notifications' => [
        'deadline_reached' => [
            'enabled' => true,
            'recipients' => ['requisitioner', 'current_assignee', 'admin'],
        ],
        'approval_completed' => [
            'enabled' => true,
            'recipients' => ['requisitioner', 'admin'],
        ],
    ],
];
```

### Upload Configuration

Edit `config/upload.php`:

```php
return [
    'max_file_size' => 10 * 1024 * 1024, // 10MB

    'allowed_mime_types' => [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ],

    'allowed_extensions' => ['pdf', 'doc', 'docx'],

    'conversion' => [
        'enabled' => true,
        'cache_ttl' => 86400, // 24 hours
        'timeout' => 90, // seconds
    ],
];
```

### Queue Configuration

Edit `config/queue.php`:

```php
'connections' => [
    'database' => [
        'driver' => 'database',
        'table' => 'jobs',
        'queue' => 'default',
        'retry_after' => 90,
        'after_commit' => false,
    ],
],

'failed' => [
    'driver' => 'database-uuids',
    'database' => env('DB_CONNECTION', 'sqlite'),
    'table' => 'failed_jobs',
],
```

### Scheduled Tasks

Edit `routes/console.php`:

```php
use App\Jobs\CheckDeadlinesJob;
use App\Jobs\CheckOverdueStages;

Schedule::job(new CheckOverdueStages)->hourly();
Schedule::job(new CheckDeadlinesJob)->hourly();
```

---

For additional information, see:

-   [System Documentation](SYSTEM_DOCUMENTATION.md)
-   [API Documentation](API_DOCUMENTATION.md)
-   [Migration Guide](WORKFLOW_ENHANCEMENTS_MIGRATION.md)
-   [Dependencies Guide](DEPENDENCIES.md)

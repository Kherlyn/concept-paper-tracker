# Implementation Plan

-   [x] 1. Extend user model and create role-based authentication system

    -   Add role and department columns to users table via migration
    -   Update User model with role enum, department field, and is_active flag
    -   Implement hasRole() and canApproveStage() methods on User model
    -   Create RoleMiddleware to protect routes based on user roles
    -   Create database seeder for users with all six roles (requisitioner, sps, vp_acad, auditor, accounting, admin)
    -   _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

-   [x] 2. Create core database schema for concept papers and workflow

    -   [x] 2.1 Create concept_papers table migration

        -   Define columns: tracking_number, requisitioner_id, department, title, nature_of_request, submitted_at, current_stage_id, status, completed_at
        -   Add foreign key constraints and indexes
        -   Implement soft deletes
        -   _Requirements: 2.1, 2.2, 2.3, 2.5_

    -   [x] 2.2 Create workflow_stages table migration

        -   Define columns: concept_paper_id, stage_name, stage_order, assigned_role, assigned_user_id, status, started_at, completed_at, deadline, remarks
        -   Add foreign key constraints and indexes
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 4.1, 4.2, 4.3_

    -   [x] 2.3 Create attachments table migration

        -   Define polymorphic columns: attachable_type, attachable_id, file_name, file_path, file_size, mime_type, uploaded_by
        -   Add indexes for polymorphic relationship
        -   _Requirements: 2.4, 6.2_

    -   [x] 2.4 Create audit_logs table migration

        -   Define columns: concept_paper_id, user_id, action, stage_name, remarks, metadata (json)
        -   Add indexes for efficient querying
        -   _Requirements: 6.3, 6.4, 6.5_

-   [x] 3. Implement Eloquent models with relationships and business logic

    -   [x] 3.1 Create ConceptPaper model

        -   Define fillable fields and casts
        -   Implement relationships: requisitioner(), stages(), attachments(), auditLogs(), currentStage()
        -   Add isOverdue() and canTransition() methods
        -   Implement automatic tracking number generation
        -   _Requirements: 2.1, 2.2, 2.3, 2.5, 5.4_

    -   [x] 3.2 Create WorkflowStage model

        -   Define fillable fields and casts
        -   Implement relationships: conceptPaper(), assignedUser(), attachments()
        -   Add isOverdue(), complete(), and return() methods
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 4.1, 4.2, 4.3, 4.4_

    -   [x] 3.3 Create Attachment model

        -   Define fillable fields and polymorphic relationship
        -   Implement attachable() morphTo relationship and uploader() belongsTo relationship
        -   Add getUrl() method for secure file access
        -   _Requirements: 2.4, 6.2_

    -   [x] 3.4 Create AuditLog model

        -   Define fillable fields with guarded timestamps
        -   Implement relationships: conceptPaper(), user()
        -   Make created_at immutable
        -   _Requirements: 6.3, 6.4, 6.5_

-   [x] 4. Create workflow configuration and service layer

    -   [x] 4.1 Create workflow configuration file

        -   Define WORKFLOW_STAGES constant with all 9 stages
        -   Include stage names, assigned roles, and max_days for each stage
        -   _Requirements: 3.1, 4.1_

    -   [x] 4.2 Implement WorkflowService

        -   Create initializeWorkflow() to create all 9 stages when concept paper is submitted
        -   Implement advanceToNextStage() to transition between stages
        -   Implement returnToPreviousStage() to handle rejections
        -   Add calculateDeadline() method using stage max_days
        -   Add checkOverdueStages() method to identify overdue items
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5, 4.1, 4.2, 4.4_

    -   [x] 4.3 Implement ConceptPaperService

        -   Create create() method to handle concept paper submission
        -   Implement attachFile() for file uploads with validation
        -   Add getStatusSummary() to generate progress data
        -   Add getUserPapers() to filter papers by user role
        -   Implement getStatistics() for admin dashboard
        -   _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 5.1, 5.4, 8.2, 8.3_

-   [x] 5. Implement authorization policies

    -   [x] 5.1 Create ConceptPaperPolicy

        -   Implement view() - requisitioner can view their own, approvers can view assigned
        -   Implement create() - only requisitioners can create
        -   Implement update() - only requisitioner before first approval
        -   Implement delete() - only admin can soft delete
        -   _Requirements: 1.3, 9.4_

    -   [x] 5.2 Create WorkflowStagePolicy

        -   Implement complete() - only assigned user with matching role can complete
        -   Implement return() - only assigned user can return to previous stage
        -   Implement addAttachment() - only assigned user can add attachments
        -   _Requirements: 1.3, 3.4, 6.1, 6.2_

    -   [x] 5.3 Create StageAccessMiddleware

        -   Verify user has permission to access specific workflow stage
        -   Check if stage is in correct status for the action
        -   _Requirements: 1.3, 3.4_

-   [x] 6. Build concept paper submission and management controllers

    -   [x] 6.1 Create ConceptPaperController

        -   Implement index() to list papers filtered by user role
        -   Implement create() to render submission form
        -   Implement store() with validation and file upload handling
        -   Implement show() to display paper details with audit trail
        -   Add update() for limited field updates
        -   Add destroy() for soft deletion (admin only)
        -   _Requirements: 2.1, 2.2, 2.3, 2.4, 2.5, 5.1, 5.4, 5.5_

    -   [x] 6.2 Create WorkflowStageController

        -   Implement show() to display stage details
        -   Implement complete() to mark stage complete and advance workflow
        -   Implement return() to send back to previous stage with remarks
        -   Implement addAttachment() to upload supporting documents
        -   _Requirements: 3.2, 3.4, 3.5, 6.1, 6.2_

    -   [x] 6.3 Create DashboardController

        -   Implement index() with role-specific dashboard views
        -   Show pending tasks, overdue items, and recent activity
        -   Add statistics() method for dashboard widgets
        -   _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

-   [x] 7. Implement notification system

    -   [x] 7.1 Create notification classes

        -   Create StageAssignedNotification for new assignments
        -   Create StageOverdueNotification for deadline reminders
        -   Create PaperCompletedNotification for workflow completion
        -   Create PaperReturnedNotification for rejections
        -   _Requirements: 7.1, 7.2_

    -   [x] 7.2 Implement NotificationService

        -   Create notifyStageAssignment() to send notifications when stage advances
        -   Implement notifyOverdue() for deadline alerts
        -   Add notifyCompletion() for workflow completion
        -   Add notifyReturn() for rejection notifications
        -   _Requirements: 7.1, 7.2, 7.5_

    -   [x] 7.3 Create NotificationController

        -   Implement index() to list user notifications
        -   Add markAsRead() to mark single notification as read
        -   Add markAllAsRead() to clear all notifications
        -   _Requirements: 7.3, 7.4_

    -   [x] 7.4 Create scheduled job for overdue checking

        -   Create CheckOverdueStages job to run hourly
        -   Query workflow stages past deadline with incomplete status
        -   Trigger notifications for overdue stages
        -   _Requirements: 4.4, 4.5, 7.2_

-   [x] 8. Build admin functionality

    -   [x] 8.1 Create AdminController for user management

        -   Implement users() to list all users with filtering
        -   Add store() to create new users with role assignment
        -   Add update() to modify user details and roles
        -   Add toggleActive() to activate/deactivate users
        -   _Requirements: 1.4, 1.5_

    -   [x] 8.2 Implement ReportService

        -   Create generateCsvExport() to export concept paper data
        -   Implement generatePdfReport() for individual paper reports
        -   Add getProcessingStatistics() for aggregate metrics
        -   Add getStageAverages() to calculate average time per stage
        -   _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

    -   [x] 8.3 Add report routes to AdminController

        -   Implement reports() to show report generation interface
        -   Add downloadCsv() to trigger CSV export
        -   Add downloadPdf() to generate PDF report
        -   _Requirements: 8.4, 8.5_

-   [x] 9. Create React frontend layout and shared components

    -   [x] 9.1 Create AuthenticatedLayout component

        -   Build responsive navigation with role-based menu items
        -   Add notification bell with unread count badge
        -   Implement user dropdown menu
        -   Add mobile sidebar navigation
        -   _Requirements: 1.3, 7.3, 10.1, 10.2_

    -   [x] 9.2 Create shared UI components

        -   Build StatusBadge component with color coding (yellow=pending, green=completed, red=overdue)
        -   Create WorkflowTimeline component for visual progress display
        -   Implement FileUpload component with drag-and-drop
        -   Build NotificationDropdown component
        -   Create ConfirmationModal for action confirmations
        -   Build DataTable component with sorting and filtering
        -   _Requirements: 5.2, 5.3, 6.1, 6.2, 7.3, 10.1, 10.3_

-   [x] 10. Build concept paper submission and viewing pages

    -   [x] 10.1 Create ConceptPaperForm page

        -   Build form with fields: requisitioner name, department, title, nature of request
        -   Implement file upload for PDF attachments
        -   Add client-side validation
        -   Handle form submission with loading states
        -   Display success/error messages
        -   _Requirements: 2.1, 2.2, 2.3, 2.4, 10.3, 10.4_

    -   [x] 10.2 Create ConceptPaperList page

        -   Display table of concept papers with filtering by status
        -   Show tracking number, title, current stage, and status badges
        -   Implement sorting by date, status, and stage
        -   Add pagination controls
        -   Include quick action buttons based on user role
        -   _Requirements: 5.1, 5.2, 5.3, 5.4, 10.1_

    -   [x] 10.3 Create ConceptPaperDetail page

        -   Display all concept paper information
        -   Render WorkflowTimeline showing all 9 stages with status
        -   Show stage cards with assigned user, deadline, and status
        -   Display audit trail in chronological order
        -   Add context-sensitive action buttons (Complete, Return, Add Attachment)
        -   Show all attachments with download links
        -   _Requirements: 5.4, 5.5, 6.1, 6.3, 6.4, 10.1_

-   [x] 11. Build workflow stage action components

    -   [x] 11.1 Create StageActionModal component

        -   Build modal for completing stages with remarks textarea
        -   Create return stage form with required remarks
        -   Add attachment upload within modal
        -   Implement form validation
        -   Handle submission with loading states
        -   _Requirements: 3.2, 3.5, 6.1, 6.2, 10.3, 10.4_

    -   [x] 11.2 Integrate stage actions into ConceptPaperDetail page

        -   Add "Complete Stage" button for assigned users
        -   Add "Return to Previous Stage" button with confirmation
        -   Add "Add Attachment" button for supporting documents
        -   Disable actions based on user permissions and stage status
        -   Show success/error feedback after actions
        -   _Requirements: 3.2, 3.4, 3.5, 6.1, 6.2_

-   [x] 12. Create role-specific dashboard pages

    -   [x] 12.1 Create Dashboard page component

        -   Build stats cards showing pending, in progress, and completed counts
        -   Display list of papers assigned to current user
        -   Show overdue items with red alert styling
        -   Add recent activity feed
        -   Implement role-specific widgets (e.g., requisitioner sees their submissions, approvers see pending approvals)
        -   _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5, 10.1_

    -   [x] 12.2 Add quick actions to dashboard

        -   Add "Submit New Paper" button for requisitioners
        -   Show "Action Required" section for approvers with pending stages
        -   Display overdue count with link to filtered list
        -   _Requirements: 5.1, 5.2, 5.3, 5.4_

-   [x] 13. Build admin pages for user and report management

    -   [x] 13.1 Create AdminUserManagement page

        -   Display user list table with role, department, and status
        -   Add "Create User" button opening modal form
        -   Implement edit user modal with role assignment dropdown
        -   Add activate/deactivate toggle switch
        -   Include search and filter by role
        -   _Requirements: 1.4, 1.5, 10.1_

    -   [x] 13.2 Create AdminReports page

        -   Build filter form for date range and status
        -   Display statistics dashboard with processing time charts
        -   Show average time per stage in table format
        -   Add "Export CSV" button triggering download
        -   Add "Generate PDF" button for selected paper
        -   Display total papers by status in pie chart
        -   _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5, 10.1_

-   [x] 14. Implement notification UI components

    -   [x] 14.1 Create NotificationBell component

        -   Display bell icon with unread count badge
        -   Show dropdown with recent notifications on click
        -   Render notification items with icon, message, and timestamp
        -   Add "Mark as Read" action for each notification
        -   Add "Mark All as Read" button
        -   Add "View All" link to full notifications page
        -   _Requirements: 7.3, 7.4, 10.1_

    -   [x] 14.2 Create Notifications page

        -   Display full list of notifications with pagination
        -   Group by read/unread status
        -   Show notification type icons
        -   Add bulk actions (mark all as read, delete)
        -   Include filtering by notification type
        -   _Requirements: 7.3, 7.4, 10.1_

-   [ ] 15. Set up file storage and handling

    -   [x] 15.1 Configure file storage

        -   Set up storage disk for concept paper attachments
        -   Create symbolic link for public access if needed
        -   Configure file size limits in validation
        -   _Requirements: 2.4, 6.2, 9.3_

    -   [x] 15.2 Implement file upload and download

        -   Create file upload handler in ConceptPaperService
        -   Implement PDF validation (MIME type, size)
        -   Generate secure file paths with unique names
        -   Create download route with authorization check
        -   Add file deletion when concept paper is deleted
        -   _Requirements: 2.4, 6.2, 9.3_

-   [x] 16. Create audit trail observer and logging

    -   [x] 16.1 Create ConceptPaperObserver

        -   Log creation event with "submitted" action
        -   Log update events with changed fields
        -   Log deletion events
        -   _Requirements: 6.3, 6.4, 6.5_

    -   [x] 16.2 Create WorkflowStageObserver

        -   Log stage completion with "completed" action
        -   Log stage return with "returned" action
        -   Log attachment additions
        -   Record user, timestamp, and remarks for all actions
        -   _Requirements: 6.3, 6.4, 6.5_

-   [x] 17. Implement form validation and error handling

    -   [x] 17.1 Create Form Request classes

        -   Create StoreConceptPaperRequest with validation rules
        -   Create CompleteStageRequest with remarks validation
        -   Create ReturnStageRequest with required remarks
        -   Create StoreUserRequest for admin user creation
        -   _Requirements: 2.1, 2.2, 2.3, 2.4, 9.2, 10.4_

    -   [x] 17.2 Add error handling to frontend

        -   Display validation errors inline on forms
        -   Show toast notifications for success/error messages
        -   Handle 403/401 errors with appropriate redirects
        -   Add error boundaries for React components
        -   _Requirements: 10.3, 10.4_

-   [x] 18. Set up routes and navigation

    -   [x] 18.1 Define web routes

        -   Create route group for authenticated users
        -   Add role-based route middleware
        -   Define routes for concept papers (index, create, store, show, update, destroy)
        -   Define routes for workflow stages (show, complete, return, addAttachment)
        -   Add dashboard route
        -   Create admin routes group (users, reports)
        -   Add notification routes
        -   _Requirements: 1.3_

    -   [x] 18.2 Update navigation menu

        -   Add "Dashboard" link for all users
        -   Add "Submit Paper" link for requisitioners
        -   Add "My Papers" link for requisitioners
        -   Add "Pending Approvals" link for approvers
        -   Add "All Papers" link for admin
        -   Add "User Management" link for admin
        -   Add "Reports" link for admin
        -   _Requirements: 1.3, 5.1_

-   [x] 19. Create database seeders for development

    -   [x] 19.1 Create comprehensive user seeder

        -   Seed one user for each role: requisitioner, sps, vp_acad, auditor, accounting, admin
        -   Set predictable passwords for development
        -   Assign appropriate departments
        -   _Requirements: 1.1, 1.2_

    -   [x] 19.2 Create sample concept paper seeder

        -   Seed 5-10 concept papers in various stages
        -   Include papers that are pending, in progress, and completed
        -   Create some overdue stages for testing
        -   Generate audit logs for each paper
        -   _Requirements: 5.1, 5.2, 5.3, 5.4_

-   [x] 20. Wire up notification email functionality

    -   [x] 20.1 Configure mail settings

        -   Set up mail driver in .env
        -   Create mail templates for notifications
        -   Configure queue for async email sending
        -   _Requirements: 7.5_

    -   [x] 20.2 Implement email notifications

        -   Add mail() method to notification classes
        -   Create Mailable classes for each notification type
        -   Design email templates with paper details and action links
        -   Test email delivery in development
        -   _Requirements: 7.5_

-   [x] 21. Add responsive design and accessibility features

    -   [x] 21.1 Implement responsive layouts

        -   Ensure all pages work on mobile, tablet, and desktop
        -   Make tables responsive with horizontal scroll or card layout
        -   Optimize navigation for mobile devices
        -   Test form usability on touch devices
        -   _Requirements: 10.1, 10.2_

    -   [x] 21.2 Add accessibility features

        -   Ensure keyboard navigation works throughout app
        -   Add ARIA labels to interactive elements
        -   Ensure color contrast meets WCAG standards
        -   Test with screen reader
        -   Add focus indicators for keyboard users
        -   _Requirements: 10.2_

-   [x] 22. Implement session management and security

    -   [ ] 22.1 Configure session settings

        -   Set session lifetime to 30 minutes
        -   Configure session driver
        -   Enable CSRF protection on all forms
        -   _Requirements: 9.1, 10.5_

    -   [x] 22.2 Add security middleware

        -   Implement rate limiting on login and submission routes
        -   Add HTTPS enforcement for production
        -   Configure secure cookie settings
        -   _Requirements: 9.1, 9.2, 9.3_

-   [ ] 23. Create initial documentation

    -   [ ] 23.1 Write README with setup instructions

        -   Document environment requirements
        -   Provide installation steps
        -   List available user roles and test credentials

        -   Include common troubleshooting tips
        -   _Requirements: All_

    -   [ ] 23.2 Document API endpoints and workflows

        -   Create API documentation for all routes
        -   Document workflow stage progression
        -   Provide examples of role-based access
        -   _Requirements: All_

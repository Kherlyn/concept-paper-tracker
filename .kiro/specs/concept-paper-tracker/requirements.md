# Requirements Document

## Introduction

The Concept Paper Tracker is a web application designed to digitize and automate the "Process Verification Form for Concept Papers with Budget Allocation." The system replaces manual paper-based routing with a digital workflow that enables requisitioners, academic officers, auditors, and accounting staff to submit, track, and approve concept papers through a structured 9-step approval process. The application aims to improve processing speed, transparency, and accountability while maintaining proper audit trails and role-based access control.

## Glossary

-   **System**: The Concept Paper Tracker web application
-   **Requisitioner**: A user who submits concept papers for approval
-   **SPS**: School Principal/Supervisor who performs initial review
-   **VP Acad**: Vice President for Academic Affairs who reviews after SPS
-   **Auditor**: User responsible for auditing review and countersigning
-   **Accounting**: User responsible for voucher and cheque preparation
-   **Admin**: System administrator who manages users and generates reports
-   **Concept Paper**: A document requesting budget allocation that requires multi-stage approval
-   **Process Flow**: The 9-step sequential approval workflow for concept papers
-   **Audit Trail**: A chronological record of all actions taken on a concept paper
-   **Nature of Request**: Classification of urgency (Regular, Urgent, Emergency)

## Requirements

### Requirement 1

**User Story:** As a system administrator, I want to manage user accounts with role-based access control, so that each user can only access functions appropriate to their role.

#### Acceptance Criteria

1. THE System SHALL provide authentication functionality for users with email and password credentials
2. THE System SHALL support six distinct user roles: Requisitioner, SPS, VP Acad, Auditor, Accounting, and Admin
3. WHEN a user logs in, THE System SHALL display only the tasks and functions assigned to their role
4. THE System SHALL allow Admin users to create, update, and deactivate user accounts
5. THE System SHALL allow Admin users to assign and modify user roles

### Requirement 2

**User Story:** As a requisitioner, I want to submit concept papers with all required details and attachments, so that my request can enter the approval workflow.

#### Acceptance Criteria

1. THE System SHALL provide a form to capture requisitioner name, department, concept paper title, and nature of request
2. WHEN a concept paper is created, THE System SHALL automatically record the submission date and time
3. THE System SHALL allow requisitioners to select nature of request from three options: Regular, Urgent, or Emergency
4. THE System SHALL allow requisitioners to upload document attachments in PDF format with a maximum file size of 10 megabytes
5. WHEN a concept paper is submitted, THE System SHALL assign a unique tracking identifier to the submission

### Requirement 3

**User Story:** As an approver, I want the system to automatically route concept papers through the defined workflow stages, so that papers move to the correct person at each step.

#### Acceptance Criteria

1. THE System SHALL implement a 9-step sequential approval process: SPS Review, VP Acad Review, Auditing Review, Acad Copy Distribution, Auditing Copy Distribution, Voucher Preparation, Audit and Countersign, Cheque Preparation, and Budget Release
2. WHEN a stage is completed, THE System SHALL automatically advance the concept paper to the next stage in the workflow
3. THE System SHALL assign each workflow stage to the appropriate user role based on predefined routing rules
4. THE System SHALL enforce sequential processing where stage N must be completed before stage N+1 can begin
5. THE System SHALL allow authorized users to return a concept paper to the previous stage with required remarks

### Requirement 4

**User Story:** As an approver, I want to track deadlines for each workflow stage, so that I can prioritize time-sensitive tasks.

#### Acceptance Criteria

1. THE System SHALL assign maximum processing time limits to each stage: SPS Review (1 day), VP Acad Review (3 days), Auditing Review (3 days), Acad Copy Distribution (1 day), Auditing Copy Distribution (1 day), Voucher Preparation (1 day), Audit and Countersign (1 day), Cheque Preparation (4 days), Budget Release (1 day)
2. WHEN a concept paper enters a stage, THE System SHALL calculate the deadline based on the current timestamp and stage time limit
3. THE System SHALL track the status of each stage as one of four states: Pending, In Progress, Completed, or Returned
4. WHEN a stage deadline is exceeded, THE System SHALL mark the stage as overdue
5. THE System SHALL display overdue stages with visual indicators to assigned users

### Requirement 5

**User Story:** As a requisitioner or approver, I want to view the current status and progress of concept papers, so that I can understand where each paper is in the approval process.

#### Acceptance Criteria

1. THE System SHALL provide a dashboard displaying all concept papers relevant to the logged-in user
2. THE System SHALL display a visual timeline or progress indicator showing completed and pending stages for each concept paper
3. THE System SHALL use color-coded status indicators: yellow for pending, green for completed, red for delayed or overdue
4. THE System SHALL display the current stage, assigned user, and deadline for each active concept paper
5. THE System SHALL allow users to view detailed information for any concept paper they have access to

### Requirement 6

**User Story:** As an approver, I want to add remarks and upload supporting documents at each stage, so that I can provide context for my decisions.

#### Acceptance Criteria

1. THE System SHALL allow authorized users to add text remarks when completing or returning a workflow stage
2. THE System SHALL allow authorized users to upload supporting documents in PDF format at any workflow stage
3. THE System SHALL record the timestamp, user identity, action taken, and remarks for every workflow action
4. THE System SHALL display the complete audit trail in chronological order for each concept paper
5. THE System SHALL prevent modification or deletion of audit trail entries after they are created

### Requirement 7

**User Story:** As a user, I want to receive notifications when concept papers require my attention, so that I can respond promptly to pending tasks.

#### Acceptance Criteria

1. WHEN a concept paper advances to a stage assigned to a user, THE System SHALL create a notification for that user
2. WHEN a stage becomes overdue, THE System SHALL create a reminder notification for the assigned user
3. THE System SHALL display unread notifications in the user interface with a notification counter
4. THE System SHALL allow users to mark notifications as read
5. WHERE email notifications are enabled, THE System SHALL send email alerts for new assignments and overdue tasks

### Requirement 8

**User Story:** As an administrator, I want to view statistics and generate reports about concept paper processing, so that I can identify bottlenecks and improve efficiency.

#### Acceptance Criteria

1. THE System SHALL provide an admin dashboard displaying aggregate statistics for all concept papers
2. THE System SHALL calculate and display average processing time for each workflow stage
3. THE System SHALL display the total number of concept papers by status: pending, in progress, completed, and returned
4. THE System SHALL allow administrators to export concept paper data in CSV format
5. THE System SHALL allow administrators to generate PDF reports containing concept paper details and audit trails

### Requirement 9

**User Story:** As a user, I want the system to maintain data integrity and security, so that sensitive information is protected and accurate.

#### Acceptance Criteria

1. THE System SHALL encrypt user passwords using industry-standard hashing algorithms
2. THE System SHALL validate all user inputs to prevent injection attacks and data corruption
3. THE System SHALL enforce file type restrictions to allow only PDF uploads for attachments
4. THE System SHALL maintain referential integrity between concept papers, users, and workflow stages
5. THE System SHALL create automatic database backups at configurable intervals

### Requirement 10

**User Story:** As a user, I want the system to be responsive and accessible, so that I can use it on different devices and screen sizes.

#### Acceptance Criteria

1. THE System SHALL render user interfaces that adapt to desktop, tablet, and mobile screen sizes
2. THE System SHALL provide navigation that is accessible via keyboard and screen readers
3. WHEN a user performs an action, THE System SHALL provide visual feedback within 2 seconds
4. THE System SHALL display error messages in clear language when validation fails
5. THE System SHALL maintain user session state for a minimum of 30 minutes of inactivity

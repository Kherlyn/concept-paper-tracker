# Requirements Document

## Introduction

This specification extends the Concept Paper Tracker system with user experience enhancements including a public landing page, enhanced user registration with academic information fields, and comprehensive user documentation. The landing page will serve as the entry point for new users, providing system overview and access to authentication. The enhanced registration will capture additional academic details such as school year and student information. The user guide will provide comprehensive documentation on system usage, role-specific workflows, and administrative functions.

## Glossary

-   **System**: The Concept Paper Tracker web application
-   **Landing Page**: The public-facing homepage accessible to unauthenticated users
-   **School Year**: The academic year designation for a user (e.g., "2024-2025", "1st Year", "2nd Year")
-   **Student Number**: A unique identifier assigned to student users
-   **User Guide**: Comprehensive documentation explaining system functionality and workflows
-   **Admin Access**: Administrative privileges for managing users and system configuration
-   **Workflow Guide**: Documentation explaining the concept paper approval process

## Requirements

### Requirement 1

**User Story:** As a visitor, I want to view a landing page that explains the system, so that I can understand its purpose before registering.

#### Acceptance Criteria

1. THE System SHALL display a public landing page at the root URL for unauthenticated users
2. THE System SHALL present an overview of the concept paper tracking workflow on the landing page
3. THE System SHALL display key features and benefits of using the system
4. THE System SHALL provide prominent "Login" and "Register" buttons on the landing page
5. WHEN an authenticated user visits the root URL, THE System SHALL redirect them to the dashboard

### Requirement 2

**User Story:** As a visitor, I want the landing page to be visually appealing and informative, so that I can quickly understand how the system works.

#### Acceptance Criteria

1. THE System SHALL display a hero section with the system title and tagline
2. THE System SHALL present a visual representation of the 9-step workflow process
3. THE System SHALL display feature cards highlighting key capabilities: submission tracking, automated routing, notifications, and reporting
4. THE System SHALL include a "How It Works" section explaining the approval process
5. THE System SHALL display role descriptions for all six user types: Requisitioner, SPS, VP Acad, Auditor, Accounting, and Admin

### Requirement 3

**User Story:** As a new user, I want to provide my school year and student information during registration, so that my academic details are captured in the system.

#### Acceptance Criteria

1. THE System SHALL add a "School Year" field to the registration form
2. THE System SHALL add a "Student Number" field to the registration form for requisitioner role
3. THE System SHALL add a "Department" field to the registration form
4. THE System SHALL validate that school year follows an acceptable format
5. THE System SHALL make student number optional for non-student roles

### Requirement 4

**User Story:** As a user, I want to see my school year and student information on my profile, so that I can verify my academic details are correct.

#### Acceptance Criteria

1. THE System SHALL display school year on the user profile page
2. THE System SHALL display student number on the user profile page when applicable
3. THE System SHALL allow users to update their school year through profile settings
4. THE System SHALL allow users to update their student number through profile settings
5. THE System SHALL display department information on the user profile

### Requirement 5

**User Story:** As an administrator, I want to view and edit user academic information, so that I can maintain accurate records.

#### Acceptance Criteria

1. THE System SHALL display school year and student number in the admin user management interface
2. THE System SHALL allow administrators to edit school year for any user
3. THE System SHALL allow administrators to edit student number for any user
4. THE System SHALL include school year and student number in user export reports
5. THE System SHALL allow filtering users by school year in the admin interface

### Requirement 6

**User Story:** As a new user, I want access to a comprehensive user guide, so that I can learn how to use the system effectively.

#### Acceptance Criteria

1. THE System SHALL provide a "User Guide" link in the main navigation menu
2. THE System SHALL display a user guide page with a table of contents
3. THE System SHALL organize the guide into sections: Getting Started, Role-Specific Workflows, and Administrative Functions
4. THE System SHALL include screenshots or diagrams illustrating key features
5. THE System SHALL make the user guide accessible to all authenticated users

### Requirement 7

**User Story:** As a requisitioner, I want documentation on how to submit concept papers, so that I can complete the submission process correctly.

#### Acceptance Criteria

1. THE System SHALL provide step-by-step instructions for creating a new concept paper
2. THE System SHALL document required fields and file upload requirements
3. THE System SHALL explain how to track submission status
4. THE System SHALL describe how to view audit trails and history
5. THE System SHALL include examples of properly completed submissions

### Requirement 8

**User Story:** As an approver, I want documentation on how to review and approve concept papers, so that I can fulfill my role responsibilities.

#### Acceptance Criteria

1. THE System SHALL provide role-specific workflow instructions for SPS, VP Acad, Auditor, and Accounting roles
2. THE System SHALL document how to complete workflow stages
3. THE System SHALL explain how to return papers to previous stages with remarks
4. THE System SHALL describe how to add attachments and supporting documents
5. THE System SHALL document deadline management and overdue notifications

### Requirement 9

**User Story:** As an administrator, I want documentation on administrative functions, so that I can manage the system effectively.

#### Acceptance Criteria

1. THE System SHALL document how to access the admin dashboard
2. THE System SHALL provide instructions for creating and managing user accounts
3. THE System SHALL explain how to assign and modify user roles
4. THE System SHALL document report generation and data export procedures
5. THE System SHALL include troubleshooting guidance for common issues

### Requirement 10

**User Story:** As a user, I want to understand the complete workflow process, so that I know what happens at each stage.

#### Acceptance Criteria

1. THE System SHALL provide a visual workflow diagram showing all 9 stages
2. THE System SHALL document the purpose and responsibilities for each workflow stage
3. THE System SHALL explain the maximum processing time for each stage
4. THE System SHALL describe what triggers stage transitions
5. THE System SHALL document the complete approval path from submission to budget release

### Requirement 11

**User Story:** As a user, I want to know how to access different parts of the system, so that I can navigate efficiently.

#### Acceptance Criteria

1. THE System SHALL document navigation structure and menu options
2. THE System SHALL explain role-based access restrictions
3. THE System SHALL provide instructions for accessing the dashboard
4. THE System SHALL document how to access notifications
5. THE System SHALL explain how to use search and filter features

### Requirement 12

**User Story:** As a user, I want FAQ documentation, so that I can find answers to common questions quickly.

#### Acceptance Criteria

1. THE System SHALL include a Frequently Asked Questions section in the user guide
2. THE System SHALL address common questions about account access and passwords
3. THE System SHALL provide answers about file upload requirements and limitations
4. THE System SHALL explain notification settings and email alerts
5. THE System SHALL include contact information for technical support

### Requirement 13

**User Story:** As a visitor on the landing page, I want to see example use cases, so that I can understand how the system benefits different users.

#### Acceptance Criteria

1. THE System SHALL display at least three use case examples on the landing page
2. THE System SHALL illustrate the requisitioner workflow with a concrete example
3. THE System SHALL demonstrate the approval process from an approver perspective
4. THE System SHALL show how administrators use reporting features
5. THE System SHALL include testimonial-style descriptions of system benefits

### Requirement 14

**User Story:** As a user, I want the login page to include helpful information, so that I know what credentials to use.

#### Acceptance Criteria

1. THE System SHALL display a link to the user guide on the login page
2. THE System SHALL provide information about default test accounts for development
3. THE System SHALL include a "Need Help?" section with support contact information
4. THE System SHALL display system status or maintenance notifications when applicable
5. THE System SHALL provide a link to password reset functionality

### Requirement 15

**User Story:** As a new user, I want the registration page to explain role selection, so that I can choose the appropriate role.

#### Acceptance Criteria

1. THE System SHALL display role descriptions next to the role selection field
2. THE System SHALL explain the difference between requisitioner and approver roles
3. THE System SHALL indicate which roles require administrative approval
4. THE System SHALL provide tooltips or help text for each registration field
5. THE System SHALL display validation requirements for each field in real-time

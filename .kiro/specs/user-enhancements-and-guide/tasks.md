# Implementation Plan

-   [x] 1. Add academic fields to user database schema

    -   Create migration to add school_year and student_number columns to users table
    -   Add nullable constraint to both fields
    -   Add unique constraint to student_number
    -   Update User model fillable array to include new fields
    -   _Requirements: 3.1, 3.2, 3.3, 4.1, 4.2, 5.1_

-

-   [x] 2. Update user registration flow

    -   [x] 2.1 Update registration form validation

        -   Modify RegisterRequest to include school_year and student_number validation rules
        -   Add custom validation messages for new fields
        -   Ensure student_number uniqueness validation
        -   _Requirements: 3.1, 3.2, 3.3, 3.5_

    -   [x] 2.2 Update RegisteredUserController

        -   Modify store() method to accept and save school_year and student_number
        -   Ensure proper data sanitization
        -   _Requirements: 3.1, 3.2, 3.3_

    -   [x] 2.3 Enhance Register.jsx component

        -   Add school_year input field with label and help text
        -   Add student_number input field (conditional on requisitioner role)
        -   Add role descriptions next to role selector
        -   Add tooltips/help text for each field
        -   Implement real-time validation feedback
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 15.1, 15.2, 15.3, 15.4, 15.5_

-   [x] 3. Update user profile display and editing

    -   [x] 3.1 Update Profile page component

        -   Display school_year on profile page
        -   Display student_number on profile page when applicable
        -   Add edit functionality for school_year
        -   Add edit functionality for student_number
        -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

    -   [x] 3.2 Update ProfileController

        -   Modify update() method to handle school_year and student_number
        -   Add validation for profile updates
        -   _Requirements: 4.3, 4.4_

-   [x] 4. Enhance admin user management

    -   [x] 4.1 Update AdminController user management

        -   Modify users() method to include school_year and student_number in response
        -   Update store() method to accept new fields when creating users
        -   Update update() method to allow editing new fields
        -   _Requirements: 5.1, 5.2, 5.3_

    -   [x] 4.2 Update AdminUserManagement.jsx component

        -   Add school_year column to user table
        -   Add student_number column to user table
        -   Include new fields in create user modal
        -   Include new fields in edit user modal
        -   Add filter by school_year functionality
        -   _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

-   [x] 5. Create landing page

    -   [x] 5.1 Create Landing.jsx page component

        -   Build main landing page structure with hero section
        -   Create responsive layout with Tailwind CSS
        -   Add navigation header with login/register buttons
        -   Implement scroll behavior and animations
        -   _Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 13.1_

    -   [x] 5.2 Create landing page sub-components

        -   Build HeroSection component with title, tagline, and CTA buttons
        -   Create FeaturesSection component displaying key features in grid
        -   Build WorkflowSection component with visual 9-step process
        -   Create RolesSection component showing all six user roles
        -   Build UseCasesSection component with example scenarios
        -   Create CTASection component with final call-to-action
        -   Build Footer component with links and information
        -   _Requirements: 1.2, 1.3, 2.1, 2.2, 2.3, 2.4, 2.5, 13.1, 13.2, 13.3, 13.4, 13.5_

    -   [x] 5.3 Configure landing page routing

        -   Update routes/web.php to serve landing page at root URL
        -   Add redirect logic for authenticated users to dashboard
        -   Ensure proper route naming
        -   _Requirements: 1.1, 1.5_

-   [x] 6. Enhance login page with helpful information

    -   Update Login.jsx to include help section
    -   Add link to user guide
    -   Add support contact information
    -   Display system status notifications area
    -   Add link to password reset
    -   _Requirements: 14.1, 14.2, 14.3, 14.4, 14.5_

-   [x] 7. Create user guide system

    -   [x] 7.1 Set up user guide infrastructure

        -   Create resources/docs/user-guide/ directory
        -   Install react-markdown npm package
        -   Create UserGuideController with index() and show() methods
        -   Add user guide routes to routes/web.php with auth middleware
        -   _Requirements: 6.1, 6.2, 11.1_

    -   [x] 7.2 Create user guide page components

        -   Build UserGuide/Index.jsx with table of contents
        -   Create UserGuide/Section.jsx for displaying individual sections
        -   Add navigation between sections (previous/next)
        -   Implement markdown rendering with proper styling
        -   Add breadcrumb navigation
        -   _Requirements: 6.1, 6.2, 6.3, 11.1, 11.2_

    -   [x] 7.3 Write getting started documentation

        -   Create getting-started.md with system overview
        -   Document login process and first-time user experience
        -   Explain navigation structure and menu options
        -   Add screenshots or diagrams for key features
        -   _Requirements: 6.1, 6.2, 6.3, 6.4, 11.1, 11.2, 11.3, 11.4, 11.5_

    -   [x] 7.4 Write requisitioner guide documentation

        -   Create requisitioner.md with submission instructions
        -   Document step-by-step paper submission process
        -   Explain tracking and status monitoring
        -   Document attachment management
        -   Include examples of properly completed submissions
        -   _Requirements: 7.1, 7.2, 7.3, 7.4, 7.5_

    -   [x] 7.5 Write approver guide documentation

        -   Create approver.md with role-specific workflows
        -   Document how to complete workflow stages
        -   Explain returning papers with remarks
        -   Document adding attachments and supporting documents
        -   Explain deadline management and overdue notifications
        -   _Requirements: 8.1, 8.2, 8.3, 8.4, 8.5_

    -   [x] 7.6 Write administrator guide documentation

        -   Create admin.md with admin dashboard access instructions
        -   Document user account creation and management
        -   Explain role assignment and modification
        -   Document report generation and data export
        -   Include troubleshooting guidance for common issues
        -   _Requirements: 9.1, 9.2, 9.3, 9.4, 9.5_

    -   [x] 7.7 Write workflow process documentation

        -   Create workflow.md with complete 9-stage workflow diagram
        -   Document purpose and responsibilities for each stage
        -   Explain maximum processing times
        -   Describe stage transition triggers
        -   Document complete approval path from submission to release
        -   _Requirements: 10.1, 10.2, 10.3, 10.4, 10.5_

    -   [x] 7.8 Write FAQ documentation

        -   Create faq.md with frequently asked questions
        -   Address account access and password questions
        -   Provide file upload requirements and limitations
        -   Explain notification settings and email alerts
        -   Include contact information for technical support
        -   _Requirements: 12.1, 12.2, 12.3, 12.4, 12.5_

-   [x] 8. Update navigation to include user guide link

    -   Add "User Guide" link to AuthenticatedLayout navigation menu
    -   Ensure link is accessible to all authenticated users
    -   Add appropriate icon for user guide link
    -   _Requirements: 6.1, 6.5, 11.1_

-   [x] 9. Add user guide link to dashboard

    -   Add quick access link to user guide on dashboard
    -   Include in help/resources section
    -   _Requirements: 6.1, 6.5_

-   [x] 10. Update database seeders

    -   Update UserSeeder to include sample school_year and student_number data
    -   Ensure test users have varied academic information
    -   _Requirements: 3.1, 3.2, 4.1, 4.2_

-   [x] 11. Create landing page styling and assets

    -   Design and implement custom Tailwind CSS classes for landing page
    -   Create or source icons for features section
    -   Optimize images for hero section
    -   Ensure responsive design for mobile, tablet, and desktop
    -   _Requirements: 1.2, 2.1, 2.2, 2.3, 2.4, 2.5_

-   [x] 12. Implement workflow visualization component

    -   Create WorkflowVisualization component for landing page
    -   Display all 9 stages with visual connections
    -   Add stage descriptions and time estimates
    -   Make component reusable for user guide
    -   _Requirements: 2.2, 2.3, 10.1, 10.2_

-   [x] 13. Add prose styling for markdown content

    -   Configure Tailwind typography plugin for markdown rendering
    -   Style code blocks, lists, and tables appropriately
    -   Ensure proper heading hierarchy
    -   Add syntax highlighting for code examples
    -   _Requirements: 6.3, 6.4_

-   [x] 14. Test and validate all new features

    -   [x] 14.1 Test landing page functionality

        -   Verify unauthenticated access to landing page
        -   Test authenticated user redirect to dashboard
        -   Verify all navigation links work correctly
        -   Test responsive design on multiple devices
        -   _Requirements: 1.1, 1.2, 1.3, 1.4, 1.5_

    -   [x] 14.2 Test registration with new fields

        -   Test registration with school_year provided
        -   Test registration with student_number provided
        -   Test registration without optional fields
        -   Verify student_number uniqueness validation
        -   Test validation error messages
        -   _Requirements: 3.1, 3.2, 3.3, 3.4, 3.5_

    -   [x] 14.3 Test profile updates

        -   Test updating school_year through profile
        -   Test updating student_number through profile
        -   Verify validation on profile updates
        -   Test display of new fields on profile page
        -   _Requirements: 4.1, 4.2, 4.3, 4.4, 4.5_

    -   [x] 14.4 Test admin user management

        -   Test creating users with new fields
        -   Test editing user academic information
        -   Test filtering users by school_year
        -   Verify new fields appear in user list
        -   _Requirements: 5.1, 5.2, 5.3, 5.4, 5.5_

    -   [x] 14.5 Test user guide functionality

        -   Test accessing user guide index
        -   Test navigating between guide sections
        -   Verify markdown rendering is correct
        -   Test previous/next navigation
        -   Verify authentication requirement
        -   _Requirements: 6.1, 6.2, 6.3, 6.4, 6.5_

-   [x] 15. Update documentation

    -   Update README with information about landing page
    -   Document new user fields in system documentation
    -   Add user guide content update procedures
    -   Document landing page customization options
    -   _Requirements: All_

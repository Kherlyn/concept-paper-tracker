# Getting Started

## System Overview

The Concept Paper Tracker is a digital workflow management system designed to streamline the approval process for concept papers with budget allocation. The system replaces manual paper-based routing with an automated 10-step approval workflow.

### Key Benefits

-   **Transparency**: Track your submission status in real-time
-   **Efficiency**: Automated routing reduces processing time
-   **Accountability**: Complete audit trail of all actions
-   **Notifications**: Stay informed about pending tasks and deadlines

### Workflow Overview Diagram

The system follows a 10-step approval process:

```mermaid
graph TD
    A[1. Requisitioner Submits Paper] --> B[2. SPS Review]
    B --> C[3. VP Acad Review]
    C --> D[4. Auditing Review]
    D --> E[5. Senior VP Approval]
    E --> F[6. Acad Copy Distribution]
    F --> G[7. Auditing Copy Distribution]
    G --> H[8. Voucher Preparation]
    H --> I[9. Audit & Countersign]
    I --> J[10. Cheque Preparation]
    J --> K[11. Budget Release - Complete]

    style A fill:#e3f2fd
    style B fill:#fff3e0
    style C fill:#fff3e0
    style D fill:#fff3e0
    style E fill:#ffe0b2
    style F fill:#fff3e0
    style G fill:#fff3e0
    style H fill:#fff3e0
    style I fill:#fff3e0
    style J fill:#fff3e0
    style K fill:#c8e6c9
```

### Who Uses This System?

The system serves seven distinct user roles:

1. **Requisitioners** - Submit and track concept papers
2. **SPS (School Principal/Supervisor)** - Initial review and approval
3. **VP Academic Affairs** - Academic review and distribution
4. **Senior VP** - Executive-level approval and oversight
5. **Auditor** - Audit review and countersigning
6. **Accounting** - Voucher and cheque preparation
7. **Administrator** - System management and reporting

#### User Roles and Responsibilities Diagram

```mermaid
graph TD
    subgraph Submission["Submission Phase"]
        R[Requisitioner<br/>Submits Papers]
    end

    subgraph Approval["Approval Phase"]
        S[SPS<br/>Initial Review]
        V[VP Academic<br/>Academic Review]
        A[Auditor<br/>Audit Review]
    end

    subgraph Processing["Processing Phase"]
        V2[VP Academic<br/>Distribution]
        A2[Auditor<br/>Distribution]
        AC1[Accounting<br/>Voucher Prep]
        A3[Auditor<br/>Countersign]
        AC2[Accounting<br/>Cheque & Release]
    end

    subgraph Management["System Management"]
        AD[Administrator<br/>User & System Management]
    end

    R --> S
    S --> V
    V --> A
    A --> V2
    V2 --> A2
    A2 --> AC1
    AC1 --> A3
    A3 --> AC2

    AD -.Manages.-> R
    AD -.Manages.-> S
    AD -.Manages.-> V
    AD -.Manages.-> A
    AD -.Manages.-> AC1

    style R fill:#e3f2fd
    style S fill:#fff3e0
    style V fill:#f3e5f5
    style A fill:#e8f5e9
    style V2 fill:#f3e5f5
    style A2 fill:#e8f5e9
    style AC1 fill:#fce4ec
    style A3 fill:#e8f5e9
    style AC2 fill:#fce4ec
    style AD fill:#ffebee
```

## Logging In

### Accessing the System

1. Navigate to the login page
2. Enter your registered email address
3. Enter your password
4. Click "Log in"

#### Login Flow Diagram

```mermaid
flowchart TD
    A[Visit System URL] --> B{Have Account?}
    B -->|No| C[Click Register]
    B -->|Yes| D[Enter Email & Password]

    C --> E[Fill Registration Form]
    E --> F[Submit Registration]
    F --> G[Email Verification]
    G --> H[Account Created]
    H --> D

    D --> I{Credentials Valid?}
    I -->|No| J[Show Error Message]
    J --> K{Forgot Password?}
    K -->|Yes| L[Password Reset Flow]
    K -->|No| D
    L --> D

    I -->|Yes| M{First Time Login?}
    M -->|Yes| N[Welcome Tour]
    M -->|No| O[Go to Dashboard]
    N --> P[Update Profile]
    P --> O

    O --> Q[Start Using System]

    style A fill:#e3f2fd
    style H fill:#c8e6c9
    style J fill:#ffcdd2
    style O fill:#c8e6c9
    style Q fill:#c8e6c9
```

### First Time Login

If this is your first time logging in:

-   You will be directed to your dashboard
-   Take a moment to familiarize yourself with the navigation
-   Check your profile to ensure your information is correct
-   Review the user guide sections relevant to your role

### Forgot Password

If you've forgotten your password:

1. Click "Forgot your password?" on the login page
2. Enter your email address
3. Check your email for a password reset link
4. Follow the instructions to create a new password
5. Return to the login page with your new password

### Account Issues

If you cannot log in:

-   Verify you're using the correct email address
-   Check that your account is active (contact your administrator)
-   Ensure your password is correct (use password reset if needed)
-   Contact technical support if problems persist

## Navigating the System

### Main Navigation

The main navigation menu provides access to key features based on your role:

#### Navigation Structure Diagram

```mermaid
graph LR
    A[Main Navigation] --> B[Dashboard]
    A --> C[Papers]
    A --> D[Management]
    A --> E[Profile]
    A --> F[User Guide]

    C --> C1[Submit Paper]
    C --> C2[My Papers]
    C --> C3[Pending Approvals]
    C --> C4[All Papers]

    D --> D1[User Management]
    D --> D2[Reports]

    E --> E1[View Profile]
    E --> E2[Edit Profile]
    E --> E3[Change Password]
    E --> E4[Log Out]

    style A fill:#1976d2,color:#fff
    style B fill:#4caf50,color:#fff
    style C fill:#ff9800,color:#fff
    style D fill:#9c27b0,color:#fff
    style E fill:#00bcd4,color:#fff
    style F fill:#607d8b,color:#fff
```

**For Requisitioners:**

-   **Dashboard** - Your personalized overview with submission statistics
-   **Submit Paper** - Create new concept paper submissions
-   **My Papers** - View and track all your submissions
-   **Profile** - Manage your account information
-   **User Guide** - Access this documentation

**For Approvers (SPS, VP Acad, Auditor, Accounting):**

-   **Dashboard** - Overview of pending tasks and statistics
-   **Pending Approvals** - Papers awaiting your action
-   **All Papers** - View all papers in the system
-   **Profile** - Manage your account information
-   **User Guide** - Access this documentation

**For Administrators:**

-   **Dashboard** - System-wide overview and statistics
-   **All Papers** - Complete view of all submissions
-   **User Management** - Create and manage user accounts
-   **Reports** - Generate reports and export data
-   **Profile** - Manage your account information
-   **User Guide** - Access this documentation

### Dashboard Overview

Your dashboard provides a personalized view of the system:

#### Dashboard Layout Diagram

```mermaid
graph TB
    subgraph Dashboard["Dashboard Layout"]
        A[Header with Notifications]
        B[Statistics Cards]
        C[Recent Activity]
        D[Quick Actions]
    end

    B --> B1[Total Papers]
    B --> B2[Pending Tasks]
    B --> B3[Completed]
    B --> B4[Overdue]

    C --> C1[Recent Submissions]
    C --> C2[Recent Approvals]
    C --> C3[Recent Updates]

    D --> D1[Submit New Paper]
    D --> D2[View Pending]
    D --> D3[View Reports]

    style Dashboard fill:#f5f5f5
    style A fill:#1976d2,color:#fff
    style B fill:#4caf50,color:#fff
    style C fill:#ff9800,color:#fff
    style D fill:#9c27b0,color:#fff
```

**Requisitioner Dashboard:**

-   Total papers submitted
-   Papers by status (Pending, In Progress, Completed, Returned)
-   Recent submissions
-   Quick access to submit new papers

**Approver Dashboard:**

-   Pending tasks requiring your action
-   Overdue items (highlighted in red)
-   Recently completed tasks
-   Quick access to pending approvals

**Administrator Dashboard:**

-   System-wide statistics
-   Total users by role
-   Papers by status
-   Recent activity
-   Quick access to management features

### Notifications

The notification bell icon in the top right corner keeps you informed:

**Notification Types:**

-   New paper assignments
-   Stage completions
-   Papers returned for revision
-   Overdue task reminders
-   System announcements

**Managing Notifications:**

1. Click the bell icon to view recent notifications
2. Click on a notification to view details
3. Mark individual notifications as read
4. Use "Mark all as read" to clear all notifications
5. Notifications are also sent via email

#### Notification Flow Diagram

```mermaid
sequenceDiagram
    participant U as User
    participant S as System
    participant E as Email Service
    participant D as Dashboard

    S->>U: Event Occurs (e.g., Paper Assigned)
    S->>D: Update Notification Bell
    S->>E: Send Email Notification

    U->>D: Click Notification Bell
    D->>U: Show Notification List

    U->>D: Click Notification
    D->>U: Navigate to Related Page

    U->>D: Mark as Read
    D->>S: Update Notification Status
    S->>D: Remove from Unread Count

    Note over U,D: Notifications also appear<br/>in email inbox
```

### Profile Menu

Access your profile menu by clicking your name in the top right:

**Profile Options:**

-   **View Profile** - See your account information
-   **Edit Profile** - Update your name, email, department, school year, and student number
-   **Change Password** - Update your password for security
-   **Log Out** - Sign out of the system

### Search and Filters

Use search and filter features to find specific papers:

**Search by:**

-   Tracking number
-   Title
-   Requisitioner name
-   Department

**Filter by:**

-   Status (Pending, In Progress, Completed, Returned)
-   Current stage
-   Date range
-   Nature of request (Regular, Urgent, Emergency)

### Understanding Status Indicators

Papers display color-coded status badges:

-   **Yellow** - Pending or In Progress
-   **Green** - Completed successfully
-   **Red** - Overdue or Returned for revision
-   **Blue** - Information or system status

#### Status Flow Diagram

```mermaid
stateDiagram-v2
    [*] --> Pending: Paper Submitted
    Pending --> InProgress: Assigned to Approver
    InProgress --> Completed: All Stages Complete
    InProgress --> Returned: Issues Found
    InProgress --> Overdue: Deadline Passed
    Returned --> Pending: Resubmitted
    Overdue --> InProgress: Completed Late
    Overdue --> Returned: Returned After Deadline
    Completed --> [*]

    note right of Pending
        Yellow Badge
        Awaiting Assignment
    end note

    note right of InProgress
        Yellow Badge
        Being Processed
    end note

    note right of Completed
        Green Badge
        Successfully Finished
    end note

    note right of Returned
        Red Badge
        Needs Revision
    end note

    note right of Overdue
        Red Badge
        Past Deadline
    end note
```

### Getting Help

If you need assistance:

1. **User Guide** - Check this documentation first
2. **FAQ Section** - Review frequently asked questions
3. **Contact Support** - Email support@example.com
4. **Administrator** - Contact your system administrator

## Next Steps

Based on your role, continue to the relevant guide section:

-   **Requisitioners** - Read the [Requisitioner Guide](#) to learn how to submit papers
-   **Approvers** - Read the [Approver Guide](#) to learn how to review and approve papers
-   **Administrators** - Read the [Administrator Guide](#) to learn about system management
-   **Everyone** - Review the [Workflow Process](#) to understand the complete approval flow

## Tips for Success

### For All Users

-   Check your dashboard daily for new tasks
-   Respond to notifications promptly
-   Keep your profile information up to date
-   Review the workflow process to understand your role

### For Requisitioners

-   Prepare all documents before starting submission
-   Provide complete and accurate information
-   Track your submissions regularly
-   Respond quickly to return requests

### For Approvers

-   Review pending tasks daily
-   Complete stages before deadlines
-   Provide clear, specific remarks when returning papers
-   Upload supporting documents when needed

### For Administrators

-   Monitor overdue stages regularly
-   Keep user accounts up to date
-   Generate reports for analysis
-   Ensure users are properly trained

## System Requirements

### Browser Compatibility

The system works best with modern browsers:

-   Google Chrome (recommended)
-   Mozilla Firefox
-   Microsoft Edge
-   Safari

### File Requirements

When uploading documents:

-   **Format**: PDF only
-   **Size**: Maximum 10MB per file
-   **Naming**: Use clear, descriptive filenames
-   **Content**: Ensure documents are readable and complete

### Internet Connection

-   Stable internet connection required
-   System saves progress automatically
-   Notifications sent in real-time

## Security Best Practices

### Password Security

-   Use a strong, unique password
-   Change your password regularly
-   Never share your password
-   Log out when finished

### Account Security

-   Verify your email address
-   Keep your profile information current
-   Report suspicious activity immediately
-   Use secure networks when accessing the system

### Data Privacy

-   Only access papers you're authorized to view
-   Don't share sensitive information
-   Follow your organization's data policies
-   Report security concerns to administrators

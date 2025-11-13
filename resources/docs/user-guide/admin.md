# Administrator Guide

## Overview

As an administrator, you have full access to the Concept Paper Tracker system. This guide covers user management, report generation, system monitoring, and troubleshooting procedures.

## Accessing the Admin Dashboard

### Admin Access Requirements

To access admin features, you must:

-   Have the "admin" role assigned to your account
-   Be logged into the system
-   Have an active account status

### Navigating to Admin Features

1. Log in with your admin credentials
2. Look for admin-specific menu items:
    - **User Management** - Create and manage user accounts
    - **Reports** - Generate reports and export data
    - **All Papers** - Complete view of all submissions

### Admin Dashboard Overview

Your dashboard provides system-wide statistics:

**Key Metrics:**

-   Total users by role
-   Total papers by status
-   Papers in progress
-   Completed papers
-   Overdue papers
-   Recent activity

**Quick Actions:**

-   Create new user
-   View all papers
-   Generate reports
-   Access user management

## Managing Users

### Viewing All Users

#### Accessing User Management

1. Click **"User Management"** in the navigation
2. You'll see a table of all users

#### User List Information

Each user displays:

-   **Name** - Full name
-   **Email** - Email address
-   **Role** - User role (Requisitioner, SPS, VP Acad, Auditor, Accounting, Admin)
-   **Department** - Department or unit
-   **School Year** - Academic year (if provided)
-   **Student Number** - Student ID (if applicable)
-   **Status** - Active or Inactive
-   **Actions** - Edit and toggle active buttons

#### Filtering and Searching Users

**Filter Options:**

-   Filter by role
-   Filter by department
-   Filter by school year
-   Filter by status (Active/Inactive)

**Search:**

-   Search by name
-   Search by email
-   Search by student number

### Creating New Users

#### Step 1: Access Create User Form

1. Click **"Create User"** button
2. The create user modal opens

#### Step 2: Fill in User Information

Complete all required fields:

**Basic Information:**

-   **Full Name** - User's complete name
-   **Email Address** - Must be unique
-   **Password** - Initial password (user can change later)

**Role Assignment:**

-   **Requisitioner** - Can submit and track papers
-   **SPS** - School Principal/Supervisor approval
-   **VP Acad** - Vice President Academic Affairs approval
-   **Auditor** - Audit review and countersigning
-   **Accounting** - Voucher and cheque preparation
-   **Admin** - Full system access

**Additional Information:**

-   **Department** - User's department or unit
-   **School Year** - Academic year (optional)
-   **Student Number** - Student ID (optional, for students)

#### Step 3: Create the User

1. Review all information for accuracy
2. Click **"Create User"**
3. User account is created
4. User receives welcome email with login instructions

#### After Creation

Once created:

-   User can log in immediately
-   User should change password on first login
-   User has access based on assigned role
-   User appears in user management list

### Editing User Information

#### Step 1: Find the User

1. Navigate to User Management
2. Use search or filters to find the user
3. Click the **"Edit"** button

#### Step 2: Modify Information

You can edit:

-   **Name** - Update full name
-   **Email** - Change email address (must be unique)
-   **Role** - Change user role
-   **Department** - Update department
-   **School Year** - Update academic year
-   **Student Number** - Update student ID

**Note**: You cannot change passwords through edit. Users must use password reset.

#### Step 3: Save Changes

1. Review your changes
2. Click **"Save Changes"**
3. Changes take effect immediately
4. User is notified of changes (if email changed)

### Assigning and Modifying Roles

#### Available Roles

**Requisitioner:**

-   Submit concept papers
-   Track submissions
-   View own papers
-   Receive notifications

**SPS (School Principal/Supervisor):**

-   Review and approve papers (Stage 1)
-   View assigned papers
-   Complete or return papers
-   Add attachments

**VP Acad (Vice President Academic Affairs):**

-   Review and approve papers (Stage 2)
-   Distribute academic copies (Stage 4)
-   View assigned papers
-   Complete or return papers

**Auditor:**

-   Audit review (Stage 3)
-   Distribute auditing copies (Stage 5)
-   Countersign vouchers (Stage 7)
-   View assigned papers

**Accounting:**

-   Prepare vouchers (Stage 6)
-   Prepare cheques (Stage 8)
-   Release budget (Stage 9)
-   View assigned papers

**Admin:**

-   Full system access
-   User management
-   View all papers
-   Generate reports
-   System configuration

#### Changing User Roles

To change a user's role:

1. Edit the user
2. Select new role from dropdown
3. Save changes
4. User's access updates immediately
5. User's pending tasks remain assigned

**Important Considerations:**

-   Changing role affects access immediately
-   Pending tasks remain with user
-   Consider reassigning tasks before role change
-   Notify user of role change

### Activating and Deactivating Users

#### Deactivating a User

**When to Deactivate:**

-   User leaves organization
-   User is on extended leave
-   Account is compromised
-   Temporary suspension needed

**How to Deactivate:**

1. Find the user in user management
2. Toggle the **"Active"** switch to OFF
3. Confirm the action
4. User is immediately deactivated

**Effects of Deactivation:**

-   User cannot log in
-   Existing assignments remain
-   Papers in progress are not affected
-   User data is preserved
-   Can be reactivated later

#### Reactivating a User

**How to Reactivate:**

1. Find the inactive user
2. Toggle the **"Active"** switch to ON
3. User can log in again
4. Previous assignments are still assigned

#### Managing Inactive User Tasks

If an inactive user has pending tasks:

1. View their assigned papers
2. Reassign to another user (if needed)
3. Or wait for user reactivation
4. Monitor for overdue items

### Bulk User Operations

#### Exporting User List

1. Navigate to User Management
2. Apply desired filters
3. Click **"Export Users"**
4. CSV file downloads with user data

**Export Includes:**

-   All user information
-   Role assignments
-   Status
-   Creation dates

#### Importing Users (if available)

If bulk import is enabled:

1. Prepare CSV file with user data
2. Follow template format
3. Upload through import function
4. Review and confirm imports

## Generating Reports

### Accessing Reports

1. Click **"Reports"** in the navigation
2. You'll see the reports dashboard

### Reports Dashboard

**Overview Statistics:**

-   Total papers by status
-   Average processing time per stage
-   Overdue papers count
-   Papers completed this month
-   Processing time trends
-   Completion rates

**Available Reports:**

-   CSV Export (all papers)
-   PDF Report (individual paper)
-   Custom date range reports
-   Status-based reports

### CSV Export

#### Generating CSV Export

1. Navigate to Reports
2. Select date range (optional)
3. Select status filter (optional)
4. Click **"Export CSV"**
5. File downloads automatically

#### CSV Contents

The export includes:

-   Tracking number
-   Title
-   Requisitioner name and email
-   Department
-   Nature of request
-   Current stage
-   Status
-   Submission date
-   Completion date (if completed)
-   All stage timestamps
-   Remarks from each stage

#### Using CSV Data

**Common Uses:**

-   External analysis in Excel
-   Import into other systems
-   Historical record keeping
-   Performance analysis
-   Reporting to stakeholders

### PDF Report

#### Generating PDF Report

1. Navigate to a concept paper
2. Click **"Generate PDF Report"**
3. PDF downloads automatically

#### PDF Contents

The report includes:

-   Complete paper information
-   Requisitioner details
-   All stage information
-   Complete audit trail
-   All remarks
-   Attachment list
-   Processing timeline

#### Using PDF Reports

**Common Uses:**

-   Archival purposes
-   Printing for records
-   Sharing with stakeholders
-   Documentation
-   Audit purposes

### Custom Reports

#### Date Range Reports

1. Select start date
2. Select end date
3. Choose report type
4. Generate report

**Use Cases:**

-   Monthly summaries
-   Quarterly reports
-   Annual reviews
-   Specific period analysis

#### Status-Based Reports

Filter by status:

-   Pending papers
-   In progress papers
-   Completed papers
-   Returned papers
-   Overdue papers

### Understanding Metrics

#### Average Processing Time

**What it shows:**

-   Average days per stage
-   Total average processing time
-   Comparison to target times

**How to use:**

-   Identify bottlenecks
-   Monitor efficiency
-   Track improvements
-   Set realistic expectations

#### Completion Rate

**What it shows:**

-   Percentage of papers completed
-   Completion trends over time
-   Success rate by department

**How to use:**

-   Measure system effectiveness
-   Identify problem areas
-   Track improvements
-   Report to leadership

#### Overdue Analysis

**What it shows:**

-   Which stages have most overdue items
-   Which users have overdue tasks
-   Overdue trends over time

**How to use:**

-   Identify problem stages
-   Address user workload issues
-   Improve processing times
-   Prevent future delays

## System Monitoring

### Monitoring Overdue Papers

#### Viewing Overdue Papers

1. Check dashboard for overdue count
2. Click to view overdue papers list
3. Papers sorted by how overdue

#### Addressing Overdue Papers

**For Each Overdue Paper:**

1. Identify assigned user
2. Check user availability
3. Contact user if needed
4. Reassign if necessary
5. Monitor until completed

**Prevention:**

-   Monitor pending tasks regularly
-   Contact users approaching deadlines
-   Ensure adequate staffing
-   Address workload issues

### Monitoring User Activity

#### Active Users

Track:

-   Login frequency
-   Task completion rates
-   Average processing times
-   Overdue task counts

#### Inactive Users

Identify:

-   Users who haven't logged in recently
-   Users with no completed tasks
-   Users with high overdue counts
-   Users who may need training

### System Health Checks

#### Daily Checks

-   Review overdue papers
-   Check for stuck papers
-   Monitor user activity
-   Review error logs (if accessible)

#### Weekly Checks

-   Review completion rates
-   Analyze processing times
-   Check user workload distribution
-   Review system performance

#### Monthly Checks

-   Generate monthly reports
-   Review trends and patterns
-   Assess system effectiveness
-   Plan improvements

## Troubleshooting

### Common User Issues

#### User Cannot Log In

**Possible Causes:**

1. Account is inactive
2. Incorrect password
3. Email not verified
4. Account doesn't exist

**Solutions:**

1. Check user status - activate if needed
2. Reset password for user
3. Verify email address
4. Create account if missing

**How to Reset Password:**

1. Edit user account
2. User must use "Forgot Password" feature
3. Or create new account with new password

#### User Not Receiving Notifications

**Possible Causes:**

1. Email configuration issue
2. Email in spam folder
3. Incorrect email address
4. Notification settings

**Solutions:**

1. Verify email address in profile
2. Check system email configuration
3. Test email sending
4. Update email if incorrect
5. Check spam filters

#### User Cannot Access Features

**Possible Causes:**

1. Incorrect role assigned
2. Account is inactive
3. Browser issues
4. System error

**Solutions:**

1. Verify role assignment
2. Check account status
3. Try different browser
4. Check error logs
5. Contact technical support

### Common Paper Issues

#### Paper Stuck in Stage

**Possible Causes:**

1. Assigned user is inactive
2. User is unavailable
3. System error
4. Workflow issue

**Solutions:**

1. Check assigned user status
2. Contact user
3. Reassign to another user
4. Check for system errors
5. Contact technical support

**How to Reassign (if feature available):**

1. View paper details
2. Click reassign option
3. Select new user
4. Confirm reassignment
5. Notify both users

#### Paper Not Advancing

**Possible Causes:**

1. Stage not completed
2. System error
3. Workflow configuration issue

**Solutions:**

1. Verify stage completion
2. Check audit trail
3. Review error logs
4. Contact technical support

#### Missing Papers

**Possible Causes:**

1. Soft deleted
2. Database issue
3. Search/filter issue

**Solutions:**

1. Check all filters
2. Search by tracking number
3. Check database directly
4. Contact technical support

### System Issues

#### Email Not Sending

**Check:**

1. Email configuration in .env file
2. Mail server status
3. Queue worker status
4. Error logs

**Solutions:**

1. Verify SMTP settings
2. Test mail configuration
3. Restart queue worker
4. Contact hosting provider

#### Slow Performance

**Possible Causes:**

1. Database size
2. Server resources
3. Network issues
4. Too many concurrent users

**Solutions:**

1. Optimize database
2. Check server resources
3. Clear cache
4. Contact hosting provider

#### File Upload Issues

**Possible Causes:**

1. File size limits
2. Storage space
3. Permission issues
4. Server configuration

**Solutions:**

1. Check upload_max_filesize in PHP
2. Verify storage space
3. Check file permissions
4. Review server logs

### Getting Technical Support

#### When to Contact Support

-   System errors you cannot resolve
-   Database issues
-   Server problems
-   Configuration issues
-   Security concerns

#### Information to Provide

When contacting support:

-   Error message (exact text)
-   User account affected
-   Timestamp of issue
-   Steps to reproduce
-   Screenshots if applicable
-   Browser and version
-   Any error logs

#### Support Contact

-   **Email**: support@example.com
-   **Include**: All relevant information
-   **Priority**: Indicate urgency level
-   **Follow-up**: Respond to support requests promptly

## Best Practices

### User Management

**Regular Maintenance:**

-   Review user list monthly
-   Deactivate departed users
-   Update role assignments as needed
-   Verify email addresses
-   Clean up inactive accounts

**Security:**

-   Enforce strong passwords
-   Review admin access regularly
-   Monitor for suspicious activity
-   Deactivate compromised accounts
-   Regular security audits

**Organization:**

-   Use consistent naming conventions
-   Keep department information current
-   Document role changes
-   Maintain user records

### System Monitoring

**Daily Tasks:**

-   Check overdue papers
-   Review dashboard statistics
-   Monitor user activity
-   Address urgent issues

**Weekly Tasks:**

-   Generate weekly reports
-   Review processing times
-   Check system health
-   Plan improvements

**Monthly Tasks:**

-   Generate monthly reports
-   Review trends
-   Assess effectiveness
-   Update documentation

### Report Generation

**Regular Reports:**

-   Generate monthly summaries
-   Track key metrics
-   Share with stakeholders
-   Archive for records

**Analysis:**

-   Identify trends
-   Find bottlenecks
-   Measure improvements
-   Set goals

**Documentation:**

-   Keep report archives
-   Document findings
-   Track changes over time
-   Share insights

## Advanced Administration

### Database Maintenance

**Regular Tasks:**

-   Backup database regularly
-   Optimize tables periodically
-   Archive old records
-   Monitor database size

**Best Practices:**

-   Schedule backups daily
-   Test restore procedures
-   Keep multiple backup copies
-   Document backup process

### System Configuration

**Environment Settings:**

-   Review .env configuration
-   Update as needed
-   Document changes
-   Test after changes

**Application Settings:**

-   Review config files
-   Update workflow settings
-   Adjust time limits if needed
-   Document customizations

### Performance Optimization

**Database:**

-   Add indexes as needed
-   Optimize slow queries
-   Archive old data
-   Regular maintenance

**Application:**

-   Clear cache regularly
-   Optimize file storage
-   Monitor resource usage
-   Update dependencies

## Tips for Success

### Effective User Management

-   **Be Proactive**: Address issues before they become problems
-   **Communicate**: Keep users informed of changes
-   **Document**: Maintain records of all changes
-   **Train**: Ensure users understand their roles
-   **Support**: Be available to help users

### Efficient Monitoring

-   **Stay Current**: Check system daily
-   **Be Responsive**: Address issues quickly
-   **Track Trends**: Monitor patterns over time
-   **Plan Ahead**: Anticipate problems
-   **Improve**: Continuously optimize processes

### Quality Reporting

-   **Be Regular**: Generate reports consistently
-   **Be Thorough**: Include all relevant data
-   **Be Clear**: Present information clearly
-   **Be Actionable**: Provide insights and recommendations
-   **Be Timely**: Share reports promptly

## Getting Help

If you need assistance:

1. **Check this guide** - Review relevant sections
2. **Check FAQ** - Look for common questions
3. **Review documentation** - Check technical docs
4. **Contact support** - Email support@example.com
5. **Consult community** - If available

## Next Steps

-   Familiarize yourself with the admin dashboard
-   Review current user list
-   Generate your first report
-   Set up monitoring routine
-   Review the [Workflow Process](#) to understand the complete system

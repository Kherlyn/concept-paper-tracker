# Workflow Process

## Overview

The Concept Paper Tracker implements a structured 9-stage sequential approval process for concept papers with budget allocation. This workflow ensures proper review, authorization, and documentation at each step from initial submission to final budget release.

Each stage has a designated responsible role, maximum processing time, and specific responsibilities. The system automatically routes papers through the workflow and tracks progress in real-time.

## Complete Workflow Diagram

```
┌─────────────────────────────────────────────────────────────────────┐
│                    CONCEPT PAPER SUBMISSION                         │
│                      (By Requisitioner)                             │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 1: SPS Review                                                │
│  Role: School Principal/Supervisor                                  │
│  Max Time: 1 day                                                    │
│  Purpose: Initial review and approval                               │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 2: VP Acad Review                                            │
│  Role: Vice President for Academic Affairs                          │
│  Max Time: 3 days                                                   │
│  Purpose: Academic review and approval                              │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 3: Auditing Review                                           │
│  Role: Auditor                                                      │
│  Max Time: 3 days                                                   │
│  Purpose: Audit review and compliance check                         │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 4: Acad Copy Distribution                                    │
│  Role: Vice President for Academic Affairs                          │
│  Max Time: 1 day                                                    │
│  Purpose: Distribute approved copy to academic department           │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 5: Auditing Copy Distribution                                │
│  Role: Auditor                                                      │
│  Max Time: 1 day                                                    │
│  Purpose: Distribute copy to auditing department                    │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 6: Voucher Preparation                                       │
│  Role: Accounting                                                   │
│  Max Time: 1 day                                                    │
│  Purpose: Prepare payment voucher                                   │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 7: Audit & Countersign                                       │
│  Role: Auditor                                                      │
│  Max Time: 1 day                                                    │
│  Purpose: Final audit and countersign voucher                       │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 8: Cheque Preparation                                        │
│  Role: Accounting                                                   │
│  Max Time: 4 days                                                   │
│  Purpose: Prepare and process cheque                                │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────────────┐
│  STAGE 9: Budget Release                                            │
│  Role: Accounting                                                   │
│  Max Time: 1 day                                                    │
│  Purpose: Release budget and complete process                       │
└────────────────────────────┬────────────────────────────────────────┘
                             │
                             ▼
                    ┌────────────────┐
                    │   COMPLETED    │
                    └────────────────┘
```

## Stage Details

### Stage 1: SPS Review

**Responsible Role:** School Principal/Supervisor (SPS)  
**Maximum Processing Time:** 1 day  
**Stage Status:** First approval stage

#### Purpose

The SPS performs the initial review of the concept paper to ensure it meets basic requirements and aligns with departmental objectives.

#### Responsibilities

-   Review concept paper title, description, and budget request
-   Verify requisitioner information and department
-   Assess alignment with school/department goals
-   Approve to advance or return with remarks for corrections
-   Add any necessary comments or supporting documentation

#### Stage Transition Triggers

-   **Advance:** SPS clicks "Complete Stage" button after review
-   **Return:** SPS clicks "Return to Requisitioner" with required remarks
-   **Automatic:** System advances to Stage 2 when completed

---

### Stage 2: VP Acad Review

**Responsible Role:** Vice President for Academic Affairs (VP Acad)  
**Maximum Processing Time:** 3 days  
**Stage Status:** Academic approval stage

#### Purpose

The VP Acad conducts a comprehensive academic review to ensure the concept paper supports institutional academic objectives and represents appropriate use of resources.

#### Responsibilities

-   Review academic merit and alignment with institutional goals
-   Assess budget reasonableness and resource allocation
-   Verify compliance with academic policies
-   Approve to advance or return with remarks
-   Add academic recommendations or conditions

#### Stage Transition Triggers

-   **Advance:** VP Acad clicks "Complete Stage" button
-   **Return:** VP Acad returns to previous stage or requisitioner with remarks
-   **Automatic:** System advances to Stage 3 when completed

---

### Stage 3: Auditing Review

**Responsible Role:** Auditor  
**Maximum Processing Time:** 3 days  
**Stage Status:** Compliance review stage

#### Purpose

The Auditor reviews the concept paper for compliance with financial policies, budget availability, and proper documentation.

#### Responsibilities

-   Verify budget availability and allocation codes
-   Review financial compliance and documentation
-   Check for proper authorization and approvals
-   Identify any audit concerns or requirements
-   Approve to advance or return with audit findings

#### Stage Transition Triggers

-   **Advance:** Auditor clicks "Complete Stage" button
-   **Return:** Auditor returns with audit findings or required corrections
-   **Automatic:** System advances to Stage 4 when completed

---

### Stage 4: Acad Copy Distribution

**Responsible Role:** Vice President for Academic Affairs (VP Acad)  
**Maximum Processing Time:** 1 day  
**Stage Status:** Distribution stage

#### Purpose

The VP Acad distributes the approved concept paper copy to the academic department for their records and implementation planning.

#### Responsibilities

-   Generate or prepare academic department copy
-   Distribute to appropriate academic personnel
-   Confirm distribution completion
-   Upload proof of distribution if required

#### Stage Transition Triggers

-   **Advance:** VP Acad confirms distribution completion
-   **Automatic:** System advances to Stage 5 when completed

---

### Stage 5: Auditing Copy Distribution

**Responsible Role:** Auditor  
**Maximum Processing Time:** 1 day  
**Stage Status:** Distribution stage

#### Purpose

The Auditor distributes the approved concept paper copy to the auditing department for their records and monitoring.

#### Responsibilities

-   Generate or prepare auditing department copy
-   Distribute to audit file or designated personnel
-   Confirm distribution completion
-   Upload proof of distribution if required

#### Stage Transition Triggers

-   **Advance:** Auditor confirms distribution completion
-   **Automatic:** System advances to Stage 6 when completed

---

### Stage 6: Voucher Preparation

**Responsible Role:** Accounting  
**Maximum Processing Time:** 1 day  
**Stage Status:** Payment processing stage

#### Purpose

The Accounting department prepares the payment voucher based on the approved concept paper and budget allocation.

#### Responsibilities

-   Create payment voucher with correct amounts
-   Assign proper account codes and budget lines
-   Attach supporting documentation
-   Prepare voucher for audit countersigning
-   Upload completed voucher to system

#### Stage Transition Triggers

-   **Advance:** Accounting confirms voucher preparation completion
-   **Automatic:** System advances to Stage 7 when completed

---

### Stage 7: Audit & Countersign

**Responsible Role:** Auditor  
**Maximum Processing Time:** 1 day  
**Stage Status:** Final audit stage

#### Purpose

The Auditor performs final audit review and countersigns the payment voucher to authorize payment processing.

#### Responsibilities

-   Review prepared voucher for accuracy
-   Verify amounts match approved budget
-   Check account codes and documentation
-   Countersign voucher to authorize payment
-   Upload countersigned voucher

#### Stage Transition Triggers

-   **Advance:** Auditor countersigns and completes stage
-   **Return:** Auditor returns to Accounting for corrections
-   **Automatic:** System advances to Stage 8 when completed

---

### Stage 8: Cheque Preparation

**Responsible Role:** Accounting  
**Maximum Processing Time:** 4 days  
**Stage Status:** Payment preparation stage

#### Purpose

The Accounting department prepares the cheque or electronic payment based on the countersigned voucher.

#### Responsibilities

-   Prepare cheque with correct payee and amount
-   Process electronic payment if applicable
-   Obtain required signatures on cheque
-   Prepare payment for release
-   Upload cheque copy or payment confirmation

#### Stage Transition Triggers

-   **Advance:** Accounting confirms cheque preparation completion
-   **Automatic:** System advances to Stage 9 when completed

---

### Stage 9: Budget Release

**Responsible Role:** Accounting  
**Maximum Processing Time:** 1 day  
**Stage Status:** Final stage

#### Purpose

The Accounting department releases the budget by delivering the cheque or completing the payment transfer, finalizing the entire approval process.

#### Responsibilities

-   Release cheque to requisitioner or designated recipient
-   Complete electronic payment transfer if applicable
-   Confirm payment delivery
-   Close out the concept paper workflow
-   Archive all documentation

#### Stage Transition Triggers

-   **Complete:** Accounting confirms budget release
-   **Automatic:** System marks concept paper as "Completed"
-   **Final:** No further stages - workflow ends

---

## Processing Timeline

### Total Maximum Processing Time

The complete workflow has a maximum processing time of **16 days** from submission to budget release:

| Stage | Name                       | Max Days | Cumulative Days |
| ----- | -------------------------- | -------- | --------------- |
| 1     | SPS Review                 | 1        | 1               |
| 2     | VP Acad Review             | 3        | 4               |
| 3     | Auditing Review            | 3        | 7               |
| 4     | Acad Copy Distribution     | 1        | 8               |
| 5     | Auditing Copy Distribution | 1        | 9               |
| 6     | Voucher Preparation        | 1        | 10              |
| 7     | Audit & Countersign        | 1        | 11              |
| 8     | Cheque Preparation         | 4        | 15              |
| 9     | Budget Release             | 1        | 16              |

### Nature of Request Impact

While the system tracks three urgency levels (Regular, Urgent, Emergency), the maximum processing times remain the same. However:

-   **Emergency** requests should be prioritized by all approvers
-   **Urgent** requests should receive expedited attention
-   **Regular** requests follow normal processing

The system displays urgency indicators to help approvers prioritize their workload.

## Stage Status Indicators

Each stage can have one of four status values:

### Pending

-   **Color:** Yellow/Amber
-   **Meaning:** Stage is waiting to be started
-   **Action:** Assigned user has not yet begun work on this stage

### In Progress

-   **Color:** Blue
-   **Meaning:** Stage is currently being worked on
-   **Action:** Assigned user is actively reviewing or processing

### Completed

-   **Color:** Green
-   **Meaning:** Stage has been successfully completed
-   **Action:** Paper has advanced to next stage

### Returned

-   **Color:** Red
-   **Meaning:** Stage was returned to previous stage or requisitioner
-   **Action:** Corrections or additional information required

### Overdue

-   **Color:** Red (with warning icon)
-   **Meaning:** Stage has exceeded maximum processing time
-   **Action:** Immediate attention required from assigned user

## Returning Papers

At any stage, authorized users can return a concept paper to a previous stage or back to the requisitioner:

### Return Requirements

1. **Remarks Required:** User must provide explanation for return
2. **Stage Selection:** User selects which stage to return to
3. **Notification:** System notifies affected user of return
4. **Audit Trail:** Return action is recorded with timestamp and remarks

### Return Process

1. User clicks "Return Paper" button on concept paper detail page
2. System displays return form with stage selection and remarks field
3. User selects target stage and enters detailed remarks
4. User confirms return action
5. System updates workflow stage and creates notification
6. Returned paper appears in target user's dashboard

### Common Return Reasons

-   Missing or incomplete information
-   Budget calculation errors
-   Policy compliance issues
-   Insufficient documentation
-   Incorrect account codes
-   Signature or authorization issues

## Approval Path Summary

### Linear Progression

The workflow follows a strict linear progression through all 9 stages. Each stage must be completed before the next stage can begin.

### No Stage Skipping

The system does not allow skipping stages. All concept papers must pass through every stage in sequence, even if a stage appears unnecessary for a particular paper.

### Role Assignments

Multiple stages may be assigned to the same role:

-   **VP Acad:** Stages 2 and 4
-   **Auditor:** Stages 3, 5, and 7
-   **Accounting:** Stages 6, 8, and 9

### Automatic Routing

When a user completes a stage, the system automatically:

1. Updates the stage status to "Completed"
2. Records completion timestamp and user
3. Advances paper to next stage
4. Assigns next stage to appropriate role
5. Calculates new deadline based on max processing time
6. Creates notification for newly assigned user
7. Updates dashboard displays for all relevant users

## Deadline Management

### Deadline Calculation

When a concept paper enters a stage:

```
Deadline = Current Timestamp + (Max Days × 24 hours)
```

### Overdue Detection

The system runs automated checks every hour to identify overdue stages:

-   Compares current time to stage deadline
-   Marks stages as overdue when deadline passes
-   Creates reminder notifications for assigned users
-   Updates visual indicators on dashboard

### Deadline Extensions

The current system does not support deadline extensions. If more time is needed:

1. User should add remarks explaining delay
2. User should complete stage as soon as possible
3. Admin can view overdue reports to track delays

## Audit Trail

Every action in the workflow is recorded in the audit trail:

### Recorded Information

-   **Timestamp:** Exact date and time of action
-   **User:** Name and role of user who performed action
-   **Action Type:** Stage completion, return, remark addition, attachment upload
-   **Stage:** Which workflow stage was affected
-   **Remarks:** Any comments or explanations provided
-   **Attachments:** References to uploaded documents

### Audit Trail Access

-   **Requisitioners:** Can view full audit trail for their submissions
-   **Approvers:** Can view audit trail for papers in their queue
-   **Admins:** Can view audit trail for all concept papers

### Audit Trail Integrity

-   Entries cannot be modified after creation
-   Entries cannot be deleted
-   All entries are timestamped with server time
-   User identity is permanently recorded

## Best Practices

### For All Users

1. **Check Dashboard Daily:** Review pending tasks and deadlines
2. **Respond Promptly:** Complete stages within maximum time limits
3. **Add Clear Remarks:** Provide detailed explanations for all actions
4. **Upload Documentation:** Attach supporting documents when relevant
5. **Monitor Notifications:** Respond to system alerts and reminders

### For Approvers

1. **Prioritize by Urgency:** Handle Emergency and Urgent requests first
2. **Review Thoroughly:** Check all details before approving
3. **Provide Feedback:** Add constructive remarks when returning papers
4. **Track Deadlines:** Monitor approaching deadlines to avoid overdue status
5. **Communicate Issues:** Contact requisitioner if clarification needed

### For Requisitioners

1. **Submit Complete Information:** Ensure all required fields are filled
2. **Attach Documentation:** Include all supporting documents
3. **Monitor Progress:** Check status regularly through dashboard
4. **Respond to Returns:** Address issues promptly when paper is returned
5. **Plan Ahead:** Submit papers with adequate time for full workflow

### For Administrators

1. **Monitor Bottlenecks:** Identify stages with frequent delays
2. **Review Reports:** Analyze processing times and completion rates
3. **Support Users:** Assist with workflow questions and issues
4. **Maintain System:** Ensure proper configuration and performance
5. **Archive Completed Papers:** Maintain organized records

## Troubleshooting

### Paper Stuck in Stage

**Problem:** Concept paper not advancing despite completion  
**Solution:**

-   Verify stage was properly completed (not just saved)
-   Check for system errors in audit trail
-   Contact administrator if issue persists

### Cannot Complete Stage

**Problem:** Complete button not working or disabled  
**Solution:**

-   Ensure all required fields are filled
-   Verify you have permission for this stage
-   Check that paper is actually assigned to you
-   Refresh page and try again

### Missing Notifications

**Problem:** Not receiving notifications for new assignments  
**Solution:**

-   Check notification settings in profile
-   Verify email address is correct
-   Check spam/junk folder for email notifications
-   Ensure notifications are enabled in system

### Incorrect Stage Assignment

**Problem:** Paper assigned to wrong user or role  
**Solution:**

-   Contact administrator to review workflow configuration
-   Verify user roles are correctly assigned
-   Check if paper was manually reassigned

### Deadline Already Passed

**Problem:** Paper assigned with deadline in the past  
**Solution:**

-   Complete stage as soon as possible
-   Add remarks explaining delay
-   Contact administrator if systemic issue

## Related Documentation

-   [Getting Started Guide](getting-started.md) - System overview and navigation
-   [Requisitioner Guide](requisitioner.md) - Submitting concept papers
-   [Approver Guide](approver.md) - Reviewing and approving papers
-   [Administrator Guide](admin.md) - System management and reporting
-   [FAQ](faq.md) - Frequently asked questions

## Support

If you have questions about the workflow process or encounter issues:

-   **Email:** support@example.com
-   **Phone:** (123) 456-7890
-   **Help Desk:** Available Monday-Friday, 8:00 AM - 5:00 PM

For urgent workflow issues affecting multiple users, contact the system administrator immediately.

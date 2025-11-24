# Workflow Enhancements Migration Summary

This document summarizes the database schema changes implemented for the workflow enhancements feature.

## Migration Files Created

### 1. `2025_11_23_000001_add_activation_fields_to_users_table.php`

**Purpose:** Add user activation tracking fields

**Changes:**

-   Added `deactivated_at` (timestamp, nullable) - Records when user was deactivated
-   Added `deactivated_by` (foreign key to users, nullable) - Records which admin deactivated the user
-   Note: `is_active` column already existed from previous migration

**Foreign Keys:**

-   `deactivated_by` references `users.id` with ON DELETE SET NULL

### 2. `2025_11_23_000002_add_workflow_enhancements_to_concept_papers_table.php`

**Purpose:** Add student involvement and deadline tracking to concept papers

**Changes:**

-   Added `students_involved` (boolean, default true) - Indicates if students are involved in the paper
-   Added `deadline_option` (varchar 50, nullable) - Stores selected deadline option (e.g., '1_week', '1_month')
-   Added `deadline_date` (timestamp, nullable) - Calculated absolute deadline date

**Indexes:**

-   `students_involved` - For filtering papers by student involvement
-   `deadline_date` - For querying papers by deadline

### 3. `2025_11_23_000003_create_annotations_table.php`

**Purpose:** Create table for document annotations and discrepancy tracking

**Schema:**

-   `id` (primary key)
-   `concept_paper_id` (foreign key to concept_papers)
-   `attachment_id` (foreign key to attachments)
-   `user_id` (foreign key to users)
-   `page_number` (integer) - Page number where annotation appears
-   `annotation_type` (varchar 50) - Type: 'marker', 'highlight', 'discrepancy'
-   `coordinates` (json) - Stores {x, y, width, height, points} for annotation position
-   `comment` (text, nullable) - Text comment associated with annotation
-   `is_discrepancy` (boolean, default false) - Flags if this is a discrepancy marker
-   `created_at`, `updated_at` (timestamps)

**Foreign Keys:**

-   `concept_paper_id` references `concept_papers.id` with ON DELETE CASCADE
-   `attachment_id` references `attachments.id` with ON DELETE CASCADE
-   `user_id` references `users.id` with ON DELETE CASCADE

**Indexes:**

-   `concept_paper_id` - For querying all annotations on a paper
-   `attachment_id` - For querying annotations on a specific document
-   `attachment_id, page_number` (compound) - For querying annotations on a specific page
-   `is_discrepancy` - For filtering discrepancy markers

### 4. `2025_11_23_000004_update_attachments_table_for_word_documents.php`

**Purpose:** Document support for Word document MIME types

**Changes:**

-   No schema changes required
-   Existing `mime_type` column (varchar 100) is sufficient
-   Documents support for additional MIME types:
    -   `application/pdf` (existing)
    -   `application/msword` (.doc files)
    -   `application/vnd.openxmlformats-officedocument.wordprocessingml.document` (.docx files)

### 5. `2025_11_23_000005_add_is_active_index_to_users_table.php`

**Purpose:** Add performance index for user activation queries

**Changes:**

-   Added index on `users.is_active` column for efficient filtering of active/inactive users

## Verification Results

All schema changes have been successfully applied and verified:

✓ Users table has activation fields (is_active, deactivated_at, deactivated_by)
✓ Concept papers table has workflow enhancement fields (students_involved, deadline_option, deadline_date)
✓ Annotations table created with all required columns and relationships
✓ Attachments table supports Word document MIME types
✓ All performance indexes created:

-   users.is_active
-   concept_papers.students_involved
-   concept_papers.deadline_date
-   annotations.concept_paper_id
-   annotations.attachment_id
-   annotations.attachment_id + page_number (compound)
-   annotations.is_discrepancy

## Requirements Satisfied

-   **Requirement 1.1:** User activation status field ✓
-   **Requirement 8.1:** Deadline selection field ✓
-   **Requirement 11.1:** Students involved field ✓
-   **Requirement 6.4:** Annotation storage with document linkage ✓

## Next Steps

The database schema is now ready for:

1. Model updates (User, ConceptPaper, Annotation models)
2. Service layer implementation (DocumentPreviewService, AnnotationService)
3. Controller updates (AdminController, ConceptPaperController, AnnotationController)
4. Frontend components (DocumentPreview, AnnotationCanvas, etc.)

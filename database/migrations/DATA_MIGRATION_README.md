# Workflow Enhancements Data Migration

## Overview

This document describes the data migration process for the workflow enhancements feature. The migration ensures that existing data in the system is properly updated to support the new features.

## Migration Command

The data migration is performed using the Artisan command:

```bash
php artisan workflow:migrate-enhancements-data
```

### Command Options

-   `--dry-run`: Run the migration without making any changes to the database. Useful for testing and verification.
-   `--force`: Skip the confirmation prompt and proceed with the migration automatically.

### Examples

```bash
# Dry run to see what will be changed
php artisan workflow:migrate-enhancements-data --dry-run

# Run the migration with confirmation prompt
php artisan workflow:migrate-enhancements-data

# Run the migration without confirmation
php artisan workflow:migrate-enhancements-data --force
```

## Migration Steps

The migration performs the following steps in order:

### 1. Backfill User Activation Status

**Purpose**: Ensure all existing users have the `is_active` field set.

**Action**: Sets `is_active = true` for all users where the field is NULL.

**Rationale**: Existing users should remain active by default to maintain current system behavior.

**Requirements**: 1.1

### 2. Backfill Student Involvement

**Purpose**: Ensure all existing concept papers have the `students_involved` field set.

**Action**: Sets `students_involved = true` for all concept papers where the field is NULL.

**Rationale**: Existing papers should follow the standard workflow including SPS Review to maintain current behavior.

**Requirements**: 11.1

### 3. Calculate and Set Deadline Dates

**Purpose**: Ensure all existing concept papers have deadline dates.

**Action**:

-   Sets `deadline_option = '1_month'` for papers without a deadline option
-   Calculates `deadline_date` as submission date + 30 days

**Rationale**: Provides a reasonable default deadline for existing papers (1 month from submission).

**Requirements**: 8.1

### 4. Update Workflow Stages for In-Progress Papers

**Purpose**: Add the new Senior VP Approval stage to papers that have already passed Auditing Review.

**Action**:

-   Identifies papers that have completed Auditing Review but don't have a Senior VP Approval stage
-   Inserts the Senior VP Approval stage between Auditing Review and the next stage
-   Increments `stage_order` for all subsequent stages
-   Updates `current_stage_id` if necessary

**Rationale**: Papers in progress need the new stage added to their workflow to comply with the updated process.

**Requirements**: 3.1

### 5. Verify Data Integrity

**Purpose**: Ensure the migration completed successfully without data corruption.

**Checks**:

-   All users have `is_active` set (not NULL)
-   All concept papers have `students_involved` set (not NULL)
-   All concept papers have `deadline_date` set (not NULL)
-   No duplicate `stage_order` values within the same concept paper

**Action**: Reports any integrity issues found.

## Pre-Migration Checklist

Before running the migration:

1. ✅ Backup the database
2. ✅ Run the migration with `--dry-run` to preview changes
3. ✅ Verify the migration summary shows expected record counts
4. ✅ Ensure no users are actively using the system
5. ✅ Test the migration on a staging environment first

## Post-Migration Verification

After running the migration:

1. ✅ Check the data integrity verification output
2. ✅ Verify a sample of users have `is_active = true`
3. ✅ Verify a sample of concept papers have `students_involved = true`
4. ✅ Verify a sample of concept papers have `deadline_date` set
5. ✅ Verify in-progress papers have the Senior VP Approval stage
6. ✅ Test the workflow with a new concept paper submission
7. ✅ Test user activation/deactivation functionality

## Rollback

If issues are encountered, the migration can be rolled back by:

1. Restoring the database from backup
2. Re-running the schema migrations if needed

Note: The migration uses database transactions, so if it fails, all changes are automatically rolled back.

## Troubleshooting

### Migration Fails with "Duplicate stage order" Error

**Cause**: Existing data has inconsistent stage ordering.

**Solution**:

1. Run the migration with `--dry-run` to identify affected papers
2. Manually fix stage ordering for affected papers
3. Re-run the migration

### Migration Shows 0 Records Affected

**Cause**: Data has already been migrated or default values were set by schema migrations.

**Solution**: This is expected if:

-   The schema migrations set default values
-   The migration has already been run
-   No existing data needs updating

### Data Integrity Check Fails

**Cause**: Migration did not complete successfully for some records.

**Solution**:

1. Review the specific integrity issues reported
2. Manually fix the affected records
3. Re-run the migration if needed

## Technical Details

### Database Transaction

The migration runs within a database transaction to ensure atomicity. If any step fails, all changes are rolled back automatically.

### Performance Considerations

-   The migration processes records in batches to avoid memory issues
-   For large datasets (>10,000 records), the migration may take several minutes
-   The migration uses Eloquent models to ensure proper event handling and data validation

### Dependencies

The migration requires:

-   Laravel 12.x
-   PHP 8.2+
-   Database with transaction support (SQLite, MySQL, PostgreSQL)

## Related Files

-   Command: `app/Console/Commands/MigrateWorkflowEnhancementsData.php`
-   Schema Migrations:
    -   `database/migrations/2025_11_23_000001_add_activation_fields_to_users_table.php`
    -   `database/migrations/2025_11_23_000002_add_workflow_enhancements_to_concept_papers_table.php`
-   Configuration: `config/workflow.php`

## Support

For issues or questions about the migration, contact the development team or refer to the main system documentation.

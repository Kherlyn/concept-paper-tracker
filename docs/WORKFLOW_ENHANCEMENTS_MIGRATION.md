# Workflow Enhancements Data Migration Guide

## Quick Start

The workflow enhancements data migration ensures that existing data in your system is properly updated to support new features including user activation, student involvement tracking, deadline management, and the Senior VP Approval stage.

### Running the Migration

```bash
# Preview what will be migrated (recommended first step)
php artisan workflow:migrate-enhancements-data --dry-run

# Run the migration with confirmation prompt
php artisan workflow:migrate-enhancements-data

# Run the migration without confirmation
php artisan workflow:migrate-enhancements-data --force
```

## What Gets Migrated

The migration performs the following operations:

### 1. User Activation Status

-   **Action**: Ensures all users have the `is_active` field set
-   **Default**: Sets `is_active = true` for any users with NULL values
-   **Impact**: Existing users remain active and can continue using the system

### 2. Student Involvement

-   **Action**: Ensures all concept papers have the `students_involved` field set
-   **Default**: Sets `students_involved = true` for any papers with NULL values
-   **Impact**: Existing papers follow the standard workflow including SPS Review

### 3. Deadline Dates

-   **Action**: Calculates and sets deadline dates for papers without them
-   **Default**: Sets deadline to submission date + 30 days (1 month)
-   **Impact**: Provides reasonable deadlines for existing papers

### 4. Senior VP Approval Stage

-   **Action**: Adds the new Senior VP Approval stage to in-progress papers
-   **Criteria**: Only affects papers that have completed Auditing Review but don't have the Senior VP stage
-   **Impact**: Updates workflow to include the new approval step

### 5. Data Integrity Verification

-   **Action**: Verifies all data was migrated correctly
-   **Checks**: Ensures no NULL values remain and no duplicate stage orders exist
-   **Impact**: Confirms migration success

## Pre-Migration Checklist

Before running the migration in production:

-   [ ] Backup your database
-   [ ] Run with `--dry-run` to preview changes
-   [ ] Test on a staging environment first
-   [ ] Ensure no users are actively using the system
-   [ ] Review the migration summary output

## Post-Migration Verification

After running the migration:

-   [ ] Check that all users have `is_active` set
-   [ ] Verify concept papers have `students_involved` set
-   [ ] Confirm deadline dates are calculated correctly
-   [ ] Test that in-progress papers have the Senior VP stage
-   [ ] Verify data integrity check passed

## Troubleshooting

### No Records Affected

If the migration shows 0 records affected, this is normal if:

-   The schema migrations already set default values
-   The migration has already been run
-   All data is already in the correct format

### Migration Fails

If the migration fails:

1. Check the error message for details
2. Verify database connectivity
3. Ensure you have proper permissions
4. Check that migrations have been run (`php artisan migrate`)

### Data Integrity Issues

If data integrity checks fail:

1. Review the specific issues reported
2. Manually inspect affected records
3. Contact support if needed

## Technical Details

-   **Transaction Safety**: The migration runs in a database transaction - if it fails, all changes are rolled back
-   **Performance**: Processes records efficiently using Eloquent models
-   **Idempotent**: Safe to run multiple times - won't create duplicate data

## Related Documentation

-   Full migration documentation: `database/migrations/DATA_MIGRATION_README.md`
-   Workflow configuration: `config/workflow.php`
-   Requirements: `.kiro/specs/workflow-enhancements/requirements.md`
-   Design: `.kiro/specs/workflow-enhancements/design.md`

## Support

For issues or questions:

1. Check the full documentation in `database/migrations/DATA_MIGRATION_README.md`
2. Review test cases in `tests/Feature/WorkflowEnhancementsDataMigrationTest.php`
3. Contact the development team

# Deadline Options Management

## Overview

The deadline options management system allows administrators to configure predefined deadline timeframes that requisitioners can select when submitting concept papers. This feature provides flexibility in managing submission deadlines while maintaining consistency across the system.

## Implementation Details

### Database Schema

A new `deadline_options` table stores the configurable deadline options:

```sql
- id: Primary key
- key: Unique identifier (e.g., '1_week', '2_months')
- label: Display name (e.g., '1 Week', '2 Months')
- days: Number of days to add to submission date
- sort_order: Display order in the UI
- timestamps: created_at, updated_at
```

### Default Options

The system is seeded with five default deadline options:

-   1 Week (7 days)
-   2 Weeks (14 days)
-   1 Month (30 days)
-   2 Months (60 days)
-   3 Months (90 days)

### API Endpoints

#### Public Endpoint (Authenticated Users)

-   `GET /deadline-options` - Retrieve all deadline options

#### Admin Endpoints

-   `GET /admin/deadline-options` - List all deadline options
-   `POST /admin/deadline-options` - Create a new deadline option
-   `PUT /admin/deadline-options/{key}` - Update an existing deadline option
-   `DELETE /admin/deadline-options/{key}` - Delete a deadline option

### Validation Rules

When creating or updating deadline options:

-   **key**: Required, alphanumeric with underscores only, max 50 characters, must be unique
-   **label**: Required, max 100 characters
-   **days**: Required, integer between 1 and 365
-   **sort_order**: Optional, integer >= 0

### Authorization

Only users with the `admin` role can create, update, or delete deadline options. All authenticated users can view the available options.

### Preserving Existing Papers

When deadline options are modified:

-   Existing concept papers retain their original `deadline_date` values
-   Only the configuration for future submissions is affected
-   The `deadline_option` field stores the key, while `deadline_date` stores the calculated date

### Usage in Concept Paper Submission

When a requisitioner submits a concept paper:

1. They select a deadline option from the available choices
2. The system looks up the selected option by key
3. The deadline date is calculated: `submission_date + option.days`
4. Both the option key and calculated date are stored with the paper

## Testing

Comprehensive tests cover:

-   CRUD operations for deadline options
-   Authorization checks (admin-only access)
-   Validation of input data
-   Preservation of existing paper deadlines
-   Integration with concept paper submission

Run tests with:

```bash
php artisan test --filter=DeadlineOptionControllerTest
```

## Migration

To apply the deadline options table:

```bash
php artisan migrate
```

The migration automatically seeds the default deadline options.

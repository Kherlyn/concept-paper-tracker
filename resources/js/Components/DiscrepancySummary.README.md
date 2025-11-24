# DiscrepancySummary Component

## Overview

The `DiscrepancySummary` component displays a comprehensive list of all discrepancies identified on a concept paper. It provides a centralized view of issues that have been flagged during document review, making it easy for stakeholders to track and address concerns.

## Features

-   **Discrepancy List**: Displays all discrepancies with complete metadata
-   **Location Information**: Shows document name and page number for each discrepancy
-   **User Attribution**: Displays who created each discrepancy and when
-   **Comment Display**: Shows the full comment text explaining the issue
-   **Navigation**: Provides "View in Document" button to jump to the discrepancy in the preview
-   **Count Badge**: Shows total number of discrepancies at a glance
-   **Empty State**: Friendly message when no discrepancies exist
-   **Error Handling**: Graceful error display with retry option
-   **Loading State**: Shows loading indicator while fetching data

## Usage

### Basic Usage

```jsx
import DiscrepancySummary from "@/Components/DiscrepancySummary";

function ConceptPaperShow({ paper }) {
    const handleNavigate = (discrepancyInfo) => {
        // Handle navigation to the discrepancy
        console.log("Navigate to:", discrepancyInfo);
    };

    return (
        <DiscrepancySummary
            conceptPaperId={paper.id}
            onNavigateToDiscrepancy={handleNavigate}
        />
    );
}
```

### With Custom Styling

```jsx
<DiscrepancySummary
    conceptPaperId={paper.id}
    onNavigateToDiscrepancy={handleNavigate}
    className="mt-6"
/>
```

## Props

| Prop                      | Type       | Default    | Description                                                  |
| ------------------------- | ---------- | ---------- | ------------------------------------------------------------ |
| `conceptPaperId`          | `number`   | `null`     | The ID of the concept paper to fetch discrepancies for       |
| `onNavigateToDiscrepancy` | `function` | `() => {}` | Callback function called when user clicks "View in Document" |
| `className`               | `string`   | `""`       | Additional CSS classes to apply to the container             |

## Navigation Callback

The `onNavigateToDiscrepancy` callback receives an object with the following properties:

```javascript
{
    attachmentId: number,      // ID of the attachment containing the discrepancy
    pageNumber: number,        // Page number where the discrepancy is located
    annotationId: number,      // ID of the annotation/discrepancy
    fileName: string          // Name of the file containing the discrepancy
}
```

## API Integration

The component fetches discrepancies from the following endpoint:

```
GET /concept-papers/{conceptPaperId}/discrepancies
```

Expected response format:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "attachment_id": 5,
            "page_number": 3,
            "comment": "Budget calculation appears incorrect",
            "created_at": "2024-01-15T10:30:00Z",
            "user": {
                "name": "John Doe"
            },
            "attachment": {
                "file_name": "concept_paper.pdf"
            }
        }
    ]
}
```

## Visual Design

### Discrepancy Card

Each discrepancy is displayed in a card with:

-   Red-themed styling to indicate issues
-   Warning icon for visual emphasis
-   Document name and page number badge
-   User attribution with timestamp
-   Full comment text
-   "View in Document" action button

### Empty State

When no discrepancies exist:

-   Success icon (checkmark in circle)
-   "No discrepancies found" message
-   Reassuring subtext

### Error State

When loading fails:

-   Error icon and message
-   "Try again" button to retry loading

## Accessibility

-   Semantic HTML structure
-   ARIA labels where appropriate
-   Keyboard navigation support
-   Screen reader friendly

## Requirements Validation

This component satisfies **Requirement 7.5** from the workflow-enhancements specification:

> "THE System SHALL display a summary list of all discrepancies identified on a concept paper"

The component provides:

-   ✅ List of all discrepancies for a concept paper
-   ✅ Discrepancy location (document, page number)
-   ✅ User who created discrepancy and timestamp
-   ✅ Comment text for each discrepancy
-   ✅ Navigation to jump to discrepancy in preview

## Integration Example

Complete integration in ConceptPapers/Show.jsx:

```jsx
import DiscrepancySummary from "@/Components/DiscrepancySummary";

export default function Show({ paper }) {
    const [showPreviewModal, setShowPreviewModal] = useState(false);
    const [previewAttachment, setPreviewAttachment] = useState(null);

    const handleNavigateToDiscrepancy = (discrepancyInfo) => {
        const attachment = paper.attachments.find(
            (att) => att.id === discrepancyInfo.attachmentId
        );

        if (attachment) {
            setPreviewAttachment(attachment);
            setShowPreviewModal(true);
        }
    };

    return (
        <div>
            {/* Other sections */}

            <DiscrepancySummary
                conceptPaperId={paper.id}
                onNavigateToDiscrepancy={handleNavigateToDiscrepancy}
            />

            {/* Preview modal */}
        </div>
    );
}
```

## Testing

The component includes comprehensive unit tests covering:

-   Loading state display
-   Empty state display
-   Discrepancy list rendering
-   Count badge display
-   Page number display
-   Document name display
-   User attribution display
-   Timestamp formatting
-   Error handling
-   Navigation callback

Run tests with:

```bash
npm test -- DiscrepancySummary.test.jsx
```

## Future Enhancements

Potential improvements:

-   Filter discrepancies by document or user
-   Sort discrepancies by date or page number
-   Export discrepancies to PDF or CSV
-   Mark discrepancies as resolved
-   Add inline resolution comments
-   Group discrepancies by document

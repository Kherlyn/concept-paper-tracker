# AnnotationCanvas Component

## Overview

The `AnnotationCanvas` component provides a canvas-based annotation system for document previews using Fabric.js. It supports multiple annotation types, touch gestures for mobile devices, and integrates with the backend API for persistence.

## Features

-   **Multiple Annotation Tools:**

    -   Marker: Draw rectangles to mark specific areas
    -   Highlight: Highlight text or sections with semi-transparent overlays
    -   Drawing: Freehand drawing for custom annotations

-   **Discrepancy Markers:**

    -   Special annotation type for flagging issues
    -   Requires a comment (enforced by validation)
    -   Visually distinct with red coloring

-   **Mobile Support:**

    -   Touch-friendly interface
    -   Pinch-to-zoom support (inherited from parent)
    -   Touch gestures for drawing and annotation

-   **Persistence:**
    -   Saves annotations to backend via API
    -   Loads existing annotations on mount
    -   Supports deletion of annotations

## Props

| Prop                  | Type     | Default  | Description                                   |
| --------------------- | -------- | -------- | --------------------------------------------- |
| `imageUrl`            | string   | null     | URL of the document image/page to annotate    |
| `pageNumber`          | number   | 1        | Current page number being annotated           |
| `conceptPaperId`      | number   | null     | ID of the concept paper (required for saving) |
| `attachmentId`        | number   | null     | ID of the attachment (required for saving)    |
| `canvasWidth`         | number   | 800      | Width of the canvas in pixels                 |
| `canvasHeight`        | number   | 1000     | Height of the canvas in pixels                |
| `onAnnotationSaved`   | function | () => {} | Callback when annotation is saved             |
| `onAnnotationDeleted` | function | () => {} | Callback when annotation is deleted           |
| `existingAnnotations` | array    | []       | Array of existing annotations to load         |
| `readOnly`            | boolean  | false    | If true, disables annotation tools            |

## Usage Example

```jsx
import AnnotationCanvas from "@/Components/AnnotationCanvas";

function DocumentViewer() {
    const [annotations, setAnnotations] = useState([]);

    const handleAnnotationSaved = (newAnnotation) => {
        setAnnotations([...annotations, newAnnotation]);
    };

    const handleAnnotationDeleted = (annotationId) => {
        setAnnotations(annotations.filter((a) => a.id !== annotationId));
    };

    return (
        <AnnotationCanvas
            pageNumber={1}
            conceptPaperId={123}
            attachmentId={456}
            canvasWidth={800}
            canvasHeight={1000}
            existingAnnotations={annotations}
            onAnnotationSaved={handleAnnotationSaved}
            onAnnotationDeleted={handleAnnotationDeleted}
        />
    );
}
```

## Annotation Data Structure

Annotations are saved with the following structure:

```javascript
{
    id: 1,
    concept_paper_id: 123,
    attachment_id: 456,
    user_id: 789,
    page_number: 1,
    annotation_type: "marker", // or "highlight", "drawing", "discrepancy"
    coordinates: {
        x: 100,
        y: 150,
        width: 200,    // for marker/highlight
        height: 50,    // for marker/highlight
        points: [[x1, y1], [x2, y2], ...] // for drawing
    },
    comment: "This section needs review",
    is_discrepancy: false,
    created_at: "2025-11-23T10:00:00Z",
    updated_at: "2025-11-23T10:00:00Z"
}
```

## Keyboard Shortcuts

-   **Delete/Backspace**: Delete selected annotation

## Mobile Gestures

-   **Tap**: Select annotation
-   **Drag**: Draw/create annotation (when tool is active)
-   **Pinch**: Zoom (handled by parent container)

## API Integration

The component integrates with the following API endpoints:

-   `POST /api/annotations` - Create new annotation
-   `DELETE /api/annotations/{id}` - Delete annotation

## Styling

The component uses Tailwind CSS for styling. Key classes:

-   Toolbar: `bg-gray-50 rounded-lg border border-gray-200`
-   Active tool: `bg-blue-600 text-white`
-   Discrepancy markers: Red color scheme (`#DC2626`)
-   Regular markers: Blue color scheme (`#3B82F6`)
-   Highlights: Yellow color scheme (`#FBBF24`)

## Requirements Validation

This component satisfies the following requirements:

-   **6.1**: Provides annotation tools (markers, highlights, drawing)
-   **6.2**: Allows visual marks on document pages
-   **6.3**: Supports text comments for annotations
-   **7.1**: Implements discrepancy markers
-   **7.2**: Enforces required comments for discrepancies
-   **7.3**: Visual distinction for discrepancy markers (red color)
-   **14.2**: Touch gesture support for mobile devices

## Notes

-   Annotations are saved immediately after creation
-   Discrepancy markers require a comment (enforced in UI and backend)
-   The canvas automatically scales to fit the document dimensions
-   Touch events are supported for mobile annotation
-   Fabric.js handles the low-level canvas operations

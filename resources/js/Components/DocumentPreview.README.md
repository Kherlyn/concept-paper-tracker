# DocumentPreview Component

A React component for previewing PDF documents with zoom, pan, and page navigation controls.

## Features

-   **PDF Rendering**: Uses PDF.js to render PDF documents in the browser
-   **Page Navigation**: Navigate through multi-page documents with previous/next buttons
-   **Zoom Controls**: Zoom in/out with buttons or mouse wheel (Ctrl+scroll)
-   **Pan Functionality**: Click and drag to pan when zoomed in
-   **Loading States**: Displays loading indicator during document conversion
-   **Error Handling**: Gracefully handles preview errors with fallback to download
-   **Responsive Design**: Works on desktop, tablet, and mobile devices
-   **Keyboard Shortcuts**: Ctrl+scroll to zoom

## Usage

```jsx
import DocumentPreview from "@/Components/DocumentPreview";

function MyComponent() {
    const [showPreview, setShowPreview] = useState(false);
    const [attachment, setAttachment] = useState(null);

    const handlePreview = (attachmentData) => {
        setAttachment(attachmentData);
        setShowPreview(true);
    };

    return (
        <>
            <button onClick={() => handlePreview(myAttachment)}>
                Preview Document
            </button>

            <DocumentPreview
                show={showPreview}
                onClose={() => setShowPreview(false)}
                attachmentId={attachment?.id}
                fileName={attachment?.file_name}
            />
        </>
    );
}
```

## Props

| Prop           | Type     | Default      | Description                               |
| -------------- | -------- | ------------ | ----------------------------------------- |
| `show`         | boolean  | `false`      | Controls modal visibility                 |
| `onClose`      | function | `() => {}`   | Callback when modal is closed             |
| `attachmentId` | number   | `null`       | ID of the attachment to preview           |
| `fileName`     | string   | `"Document"` | Name of the file to display in the header |

## Backend Requirements

The component expects the following backend routes:

-   `attachments.preview` - GET route that returns PDF content
-   `attachments.download` - GET route that downloads the original file

The preview endpoint should:

-   Accept an attachment ID parameter
-   Return PDF content with `Content-Type: application/pdf`
-   Return JSON error response if preview fails
-   Support Word document conversion to PDF

## Controls

### Mouse Controls

-   **Click & Drag**: Pan the document when zoomed in
-   **Ctrl + Scroll**: Zoom in/out

### Button Controls

-   **Previous/Next**: Navigate between pages
-   **Zoom In/Out**: Adjust zoom level (50% - 300%)
-   **Reset**: Reset zoom to 100%
-   **Download**: Download the original file

## Error Handling

The component handles the following error scenarios:

1. **Document Load Failure**: Shows error message with download option
2. **Conversion Failure**: Backend returns JSON error, component displays message
3. **Network Errors**: Catches and displays network-related errors
4. **Rendering Errors**: Handles PDF.js rendering failures

## Dependencies

-   `pdfjs-dist`: PDF.js library for rendering PDFs
-   `@headlessui/react`: For modal component
-   `@inertiajs/react`: For route helper

## Browser Compatibility

-   Modern browsers with Canvas API support
-   PDF.js worker loaded from CDN
-   Tested on Chrome, Firefox, Safari, and Edge

## Performance Considerations

-   PDF documents are loaded asynchronously
-   Pages are rendered on-demand (not all at once)
-   Canvas rendering is optimized for performance
-   Loading indicators prevent UI blocking

## Accessibility

-   ARIA labels on all interactive elements
-   Keyboard navigation support
-   Screen reader friendly
-   Focus management for modal

## Future Enhancements

-   Touch gestures for mobile (pinch-to-zoom)
-   Annotation support (markers, highlights)
-   Full-screen mode
-   Print functionality
-   Page thumbnails sidebar

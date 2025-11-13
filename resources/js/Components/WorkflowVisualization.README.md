# WorkflowVisualization Component

A reusable React component that displays the 9-step concept paper approval workflow with visual connections, stage descriptions, and time estimates.

## Features

-   **Visual Timeline**: Displays all 9 workflow stages in a vertical timeline format
-   **Stage Information**: Shows stage name, assigned role, processing time, and description
-   **Flexible Variants**: Supports multiple display variants (default, minimal, detailed)
-   **Customizable**: Control visibility of roles, descriptions, and other elements
-   **Responsive**: Works well on all screen sizes
-   **Reusable**: Can be used in landing pages, user guides, and other contexts

## Usage

### Basic Usage

```jsx
import WorkflowVisualization from "@/Components/WorkflowVisualization";

function MyComponent() {
    return <WorkflowVisualization />;
}
```

### With Custom Stages

```jsx
const customStages = [
    {
        number: 1,
        name: "Stage Name",
        role: "Assigned Role",
        maxDays: 2,
        description: "Stage description",
    },
    // ... more stages
];

<WorkflowVisualization stages={customStages} />;
```

### Compact Layout

```jsx
<WorkflowVisualization compact={true} />
```

### Minimal Variant

```jsx
<WorkflowVisualization variant="minimal" />
```

### Detailed Variant (with additional notes)

```jsx
<WorkflowVisualization variant="detailed" />
```

### Hide Roles or Descriptions

```jsx
<WorkflowVisualization showRoles={false} showDescriptions={false} />
```

## Props

| Prop               | Type    | Default       | Description                                         |
| ------------------ | ------- | ------------- | --------------------------------------------------- |
| `stages`           | Array   | defaultStages | Array of workflow stage objects                     |
| `compact`          | Boolean | false         | Use compact layout without summary                  |
| `showRoles`        | Boolean | true          | Show role information for each stage                |
| `showDescriptions` | Boolean | true          | Show stage descriptions                             |
| `variant`          | String  | 'default'     | Visual variant: 'default', 'minimal', or 'detailed' |
| `className`        | String  | ''            | Additional CSS classes                              |

## Stage Object Structure

Each stage object should have the following properties:

```javascript
{
    number: 1,              // Stage number (1-9)
    name: "Stage Name",     // Display name of the stage
    role: "Role Name",      // Assigned role (e.g., "SPS", "VP Academic Affairs")
    maxDays: 1,            // Maximum processing time in days
    description: "...",     // Brief description of the stage
    details: []            // Optional: Array of additional details (used in 'detailed' variant)
}
```

## Variants

### Default

-   Shows all stage information
-   Includes summary with total processing time
-   Standard spacing and sizing

### Minimal

-   Smaller stage indicators
-   Reduced spacing
-   Ideal for sidebars or compact spaces

### Detailed

-   All features of default variant
-   Additional footer with important notes
-   Supports optional `details` array in stage objects
-   Best for comprehensive documentation

## Examples

### Landing Page Usage

```jsx
// In WorkflowSection.jsx
import WorkflowVisualization from "@/Components/WorkflowVisualization";

export default function WorkflowSection() {
    return (
        <section className="py-20">
            <div className="container mx-auto">
                <h2>9-Step Approval Process</h2>
                <WorkflowVisualization />
            </div>
        </section>
    );
}
```

### User Guide Usage

```jsx
// In UserGuide/Section.jsx
import WorkflowVisualization from "@/Components/WorkflowVisualization";

export default function UserGuideSection({ section }) {
    const isWorkflowSection = section === "workflow";

    return (
        <div>
            {isWorkflowSection && <WorkflowVisualization variant="detailed" />}
            {/* Other content */}
        </div>
    );
}
```

### Dashboard Widget

```jsx
// Compact version for dashboard
<WorkflowVisualization
    compact={true}
    variant="minimal"
    showDescriptions={false}
    className="max-w-md"
/>
```

## Styling

The component uses Tailwind CSS classes and follows the application's design system:

-   **Primary Color**: Indigo (indigo-600, indigo-50, etc.)
-   **Text Colors**: Gray scale (gray-900, gray-600, gray-500)
-   **Spacing**: Consistent with Tailwind spacing scale
-   **Shadows**: Subtle shadows for depth (shadow-sm, shadow-md)

### Custom Styling

You can add custom classes via the `className` prop:

```jsx
<WorkflowVisualization className="my-custom-class max-w-2xl mx-auto" />
```

## Accessibility

-   Uses semantic HTML structure
-   Proper heading hierarchy
-   Color contrast meets WCAG AA standards
-   Icons include descriptive context

## Performance

-   Lightweight component with minimal dependencies
-   Only uses Heroicons for icons
-   No external API calls
-   Efficient rendering with React

## Integration Points

This component is currently integrated in:

1. **Landing Page** (`resources/js/Components/Landing/WorkflowSection.jsx`)

    - Shows the workflow to visitors
    - Uses default variant

2. **User Guide** (`resources/js/Pages/UserGuide/Section.jsx`)
    - Displays in the workflow documentation section
    - Uses detailed variant with additional notes

## Future Enhancements

Potential improvements for future versions:

-   [ ] Interactive stage highlighting
-   [ ] Animation on scroll
-   [ ] Current stage indicator (for tracking active papers)
-   [ ] Expandable stage details
-   [ ] Print-friendly styling
-   [ ] Export as image/PDF
-   [ ] Localization support

## Related Components

-   `WorkflowSection.jsx` - Landing page section using this component
-   `StatusBadge.jsx` - Status indicators for concept papers
-   `WorkflowTimeline.jsx` - Timeline view in paper details

## Support

For issues or questions about this component, please refer to:

-   Design document: `.kiro/specs/user-enhancements-and-guide/design.md`
-   Requirements: `.kiro/specs/user-enhancements-and-guide/requirements.md`
-   Task list: `.kiro/specs/user-enhancements-and-guide/tasks.md`

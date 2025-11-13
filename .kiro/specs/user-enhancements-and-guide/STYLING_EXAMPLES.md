# Prose Styling Examples

This document shows examples of how different markdown elements will be styled in the User Guide.

## Headings

The heading hierarchy is properly styled with:

-   H1: 2.25em, bold (700), gray-900
-   H2: 1.5em, semibold (600), gray-900, with bottom border
-   H3: 1.25em, semibold (600), gray-900
-   H4: 1em, semibold (600), gray-900

## Text Formatting

**Bold text** appears in gray-900 with font-weight 600.

_Italic text_ appears in gray-700 with italic style.

[Links](https://example.com) appear in indigo-600 with underline and hover to indigo-800.

## Code

Inline `code` appears with indigo-600 color on gray-100 background with rounded corners.

Code blocks have dark theme:

```javascript
function greet(name) {
    console.log(`Hello, ${name}!`);
    return true;
}
```

```php
class User extends Model
{
    protected $fillable = ['name', 'email'];

    public function papers()
    {
        return $this->hasMany(ConceptPaper::class);
    }
}
```

## Lists

Unordered lists use indigo-500 bullets:

-   First item
-   Second item
    -   Nested item
    -   Another nested item
-   Third item

Ordered lists use gray-500 numbers:

1. First step
2. Second step
    1. Sub-step A
    2. Sub-step B
3. Third step

## Tables

Tables are styled with borders, hover effects, and proper spacing:

| Column 1 | Column 2 | Column 3 |
| -------- | -------- | -------- |
| Data 1   | Data 2   | Data 3   |
| Data 4   | Data 5   | Data 6   |

## Blockquotes

> This is a blockquote with indigo-500 left border and indigo-50 background.
> It can span multiple lines and maintains proper formatting.

## Task Lists

-   [x] Completed task
-   [ ] Pending task
-   [ ] Another pending task

## Horizontal Rules

---

## Images

Images will have rounded corners and shadows (when added to markdown files).

## Syntax Highlighting Colors

The following syntax elements are color-coded:

-   **Keywords** (if, else, function, class): Purple (c084fc)
-   **Strings**: Green (34d399)
-   **Numbers**: Yellow (fbbf24)
-   **Comments**: Gray (9ca3af), italic
-   **Functions**: Blue (60a5fa)
-   **Variables**: Yellow (fbbf24)
-   **Types/Classes**: Yellow (fbbf24)

## Responsive Design

All elements are responsive:

-   Tables scroll horizontally on mobile
-   Code blocks scroll horizontally when needed
-   Proper spacing on all screen sizes
-   Touch-friendly on mobile devices

## Accessibility Features

-   Proper color contrast ratios
-   Semantic HTML structure
-   Keyboard navigation support
-   Screen reader friendly
-   Focus indicators on interactive elements

## Print Styles

When printing:

-   Code blocks use light background
-   Links show in black
-   Proper page breaks
-   Optimized for paper output

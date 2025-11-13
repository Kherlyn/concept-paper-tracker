# Customization Guide

This guide explains how to customize various aspects of the Concept Paper Tracker system.

## Landing Page Customization

### Component Structure

The landing page is built with modular React components:

```
resources/js/Pages/
├── Landing.jsx                 # Main landing page component
└── Landing/
    ├── HeroSection.jsx        # Hero banner with title and CTA
    ├── FeaturesSection.jsx    # Feature cards grid
    ├── WorkflowSection.jsx    # Workflow visualization
    ├── RolesSection.jsx       # User roles display
    ├── UseCasesSection.jsx    # Example scenarios
    ├── CTASection.jsx         # Final call-to-action
    └── Footer.jsx             # Footer with links
```

### Customizing Content

#### Hero Section

Edit `resources/js/Pages/Landing/HeroSection.jsx`:

```jsx
// Change title
<h1 className="text-5xl font-bold text-gray-900 mb-6">
    Your Custom Title Here
</h1>

// Change tagline
<p className="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
    Your custom description here
</p>
```

#### Features Section

Edit `resources/js/Pages/Landing/FeaturesSection.jsx`:

```jsx
const features = [
    {
        title: "Your Feature",
        description: "Feature description",
        icon: "workflow", // Icon name
    },
    // Add more features
];
```

Available icons from Heroicons:

-   `DocumentTextIcon`
-   `ClockIcon`
-   `BellIcon`
-   `ChartBarIcon`
-   `UserGroupIcon`
-   `ShieldCheckIcon`

#### Workflow Visualization

The workflow data comes from `config/workflow.php`. To customize:

```php
// config/workflow.php
return [
    'stages' => [
        [
            'name' => 'Your Stage Name',
            'role' => 'role_name',
            'duration_days' => 1,
            'description' => 'Stage description',
        ],
        // Add more stages
    ],
];
```

#### Roles Section

Edit `resources/js/Pages/Landing/RolesSection.jsx`:

```jsx
const roles = [
    {
        name: "Role Name",
        description: "Role description",
        icon: "user",
    },
    // Modify or add roles
];
```

#### Use Cases Section

Edit `resources/js/Pages/Landing/UseCasesSection.jsx`:

```jsx
const useCases = [
    {
        title: "Use Case Title",
        description: "Detailed description",
        persona: "User Type",
    },
    // Add more use cases
];
```

### Styling Customization

#### Colors

The landing page uses Tailwind CSS. To change colors:

1. **Primary Color**: Search for `indigo` and replace with your color
2. **Background Gradients**: Modify gradient classes in `Landing.jsx`

```jsx
// Example: Change from indigo to blue
className="bg-indigo-600" → className="bg-blue-600"
className="text-indigo-600" → className="text-blue-600"
```

#### Typography

Modify font sizes and weights:

```jsx
// Hero title
className = "text-5xl font-bold"; // Change to text-6xl for larger

// Section headings
className = "text-3xl font-bold"; // Change to text-4xl for larger
```

#### Spacing

Adjust padding and margins:

```jsx
// Section padding
className = "py-20"; // Change to py-16 or py-24

// Container spacing
className = "mb-8"; // Change margin-bottom
```

### Adding Images

To add images to the landing page:

1. **Place images**: Add to `public/images/landing/`
2. **Reference in component**:

```jsx
<img
    src="/images/landing/your-image.jpg"
    alt="Description"
    className="w-full h-auto rounded-lg shadow-lg"
/>
```

### Responsive Design

The landing page is mobile-first. Customize breakpoints:

```jsx
// Mobile: default
// Tablet: md: prefix
// Desktop: lg: prefix

className = "grid md:grid-cols-2 lg:grid-cols-4";
// Mobile: 1 column
// Tablet: 2 columns
// Desktop: 4 columns
```

## User Registration Customization

### Adding Custom Fields

To add new fields to registration:

1. **Update migration**:

```php
// database/migrations/xxxx_add_custom_fields_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->string('custom_field')->nullable();
});
```

2. **Update User model**:

```php
// app/Models/User.php
protected $fillable = [
    // ... existing fields
    'custom_field',
];
```

3. **Update validation**:

```php
// app/Http/Requests/Auth/RegisterRequest.php
public function rules(): array
{
    return [
        // ... existing rules
        'custom_field' => ['nullable', 'string', 'max:255'],
    ];
}
```

4. **Update Register component**:

```jsx
// resources/js/Pages/Auth/Register.jsx
const { data, setData, post } = useForm({
    // ... existing fields
    custom_field: "",
});

// Add input field in form
<TextInput
    id="custom_field"
    value={data.custom_field}
    onChange={(e) => setData("custom_field", e.target.value)}
/>;
```

### Customizing Role Descriptions

Edit `resources/js/Pages/Auth/Register.jsx`:

```jsx
const roleDescriptions = {
    requisitioner: "Your custom description",
    sps: "Your custom description",
    // ... other roles
};
```

### Validation Rules

Customize validation in `app/Http/Requests/Auth/RegisterRequest.php`:

```php
public function rules(): array
{
    return [
        'school_year' => [
            'nullable',
            'string',
            'max:50',
            'regex:/^\d{4}-\d{4}$/', // Enforce format: 2024-2025
        ],
        'student_number' => [
            'nullable',
            'string',
            'max:50',
            'unique:users',
            'regex:/^\d{4}-\d{5}$/', // Enforce format: 2024-00001
        ],
    ];
}
```

## User Guide Customization

### Content Organization

User guide content is stored in `resources/docs/user-guide/`:

```
resources/docs/user-guide/
├── getting-started.md      # System overview and basics
├── requisitioner.md        # Requisitioner workflows
├── approver.md            # Approver workflows
├── admin.md               # Administrator guide
├── workflow.md            # Workflow documentation
└── faq.md                 # Frequently asked questions
```

### Markdown Formatting

Use standard Markdown syntax:

```markdown
# Main Heading

## Section Heading

### Subsection Heading

**Bold text**
_Italic text_

-   Bullet point
-   Another point

1. Numbered list
2. Second item

[Link text](https://example.com)

![Image alt text](/images/user-guide/screenshot.png)

`inline code`

\`\`\`php
// Code block
echo "Hello World";
\`\`\`

| Column 1 | Column 2 |
| -------- | -------- |
| Data 1   | Data 2   |
```

### Adding Screenshots

1. **Capture screenshots**: Use your preferred tool
2. **Save images**: Place in `public/images/user-guide/`
3. **Reference in Markdown**:

```markdown
![Dashboard Screenshot](/images/user-guide/dashboard.png)
```

### Adding New Sections

To add a new guide section:

1. **Create Markdown file**: `resources/docs/user-guide/new-section.md`

2. **Update controller**: Edit `app/Http/Controllers/UserGuideController.php`

```php
private function getSections(): array
{
    return [
        // ... existing sections
        'new-section' => [
            'title' => 'New Section Title',
            'icon' => 'document', // Icon name
            'subsections' => [
                'topic1' => 'Topic 1 Title',
                'topic2' => 'Topic 2 Title',
            ],
        ],
    ];
}
```

3. **Add route** (optional): If you need custom routing

```php
// routes/web.php
Route::get('/user-guide/new-section', [UserGuideController::class, 'showNewSection'])
    ->name('user-guide.new-section');
```

### Customizing Guide Layout

Edit `resources/js/Pages/UserGuide/Index.jsx` and `Section.jsx`:

```jsx
// Change grid layout
className = "grid gap-6 md:grid-cols-2 lg:grid-cols-3";
// Change to: md:grid-cols-3 lg:grid-cols-4 for more columns

// Modify card styling
className = "block p-6 bg-white rounded-lg shadow-sm hover:shadow-md";
```

### Adding Search Functionality

To add search to the user guide:

1. **Install search library**: `npm install fuse.js`

2. **Create search component**:

```jsx
// resources/js/Components/UserGuideSearch.jsx
import Fuse from "fuse.js";

export default function UserGuideSearch({ sections }) {
    const [query, setQuery] = useState("");
    const [results, setResults] = useState([]);

    // Implement search logic
}
```

3. **Add to Index page**: Include search component in `UserGuide/Index.jsx`

## Admin Interface Customization

### User Management Columns

Edit `resources/js/Pages/Admin/UserManagement.jsx`:

```jsx
// Add or remove columns
const columns = [
    { key: "name", label: "Name" },
    { key: "email", label: "Email" },
    { key: "role", label: "Role" },
    { key: "department", label: "Department" },
    { key: "school_year", label: "School Year" },
    { key: "student_number", label: "Student Number" },
    // Add custom columns
];
```

### Filtering Options

Add custom filters in `AdminController.php`:

```php
public function users(Request $request)
{
    $query = User::query();

    // Add custom filters
    if ($request->filled('custom_filter')) {
        $query->where('custom_field', $request->custom_filter);
    }

    return $query->paginate(15);
}
```

## Email Notification Customization

### Email Templates

Email templates are located in `resources/views/emails/`:

```
resources/views/emails/
├── stage-assigned.blade.php
├── stage-overdue.blade.php
├── stage-completed.blade.php
└── stage-returned.blade.php
```

Customize templates using Blade syntax:

```blade
<h1>Custom Email Title</h1>
<p>Hello {{ $user->name }},</p>
<p>Your custom message here.</p>
```

### Notification Content

Edit notification classes in `app/Notifications/`:

```php
// app/Notifications/StageAssignedNotification.php
public function toMail($notifiable)
{
    return (new MailMessage)
        ->subject('Custom Subject')
        ->line('Custom message line')
        ->action('Custom Button Text', $url)
        ->line('Custom closing line');
}
```

## Workflow Customization

### Modifying Stages

Edit `config/workflow.php`:

```php
return [
    'stages' => [
        [
            'name' => 'Custom Stage Name',
            'role' => 'role_name',
            'duration_days' => 2,
            'description' => 'Stage description',
            'order' => 1,
        ],
        // Add, remove, or modify stages
    ],
];
```

### Stage Duration

Change default durations:

```php
'duration_days' => 3, // Change from 1 to 3 days
```

### Adding Stage Actions

To add custom actions for stages:

1. **Update WorkflowStageController**:

```php
public function customAction(WorkflowStage $stage)
{
    // Custom logic
    $stage->update(['custom_field' => 'value']);

    return back()->with('success', 'Action completed');
}
```

2. **Add route**:

```php
Route::post('/workflow-stages/{stage}/custom-action',
    [WorkflowStageController::class, 'customAction'])
    ->name('workflow-stages.custom-action');
```

3. **Add button in UI**:

```jsx
<button onClick={() => handleCustomAction(stage.id)}>Custom Action</button>
```

## Styling and Theming

### Global Color Scheme

Edit `tailwind.config.js`:

```js
module.exports = {
    theme: {
        extend: {
            colors: {
                primary: {
                    50: "#f0f9ff",
                    100: "#e0f2fe",
                    // ... define your color palette
                    900: "#0c4a6e",
                },
            },
        },
    },
};
```

### Custom Fonts

1. **Add font files**: Place in `public/fonts/`

2. **Update CSS**: Edit `resources/css/app.css`

```css
@font-face {
    font-family: "CustomFont";
    src: url("/fonts/custom-font.woff2") format("woff2");
}

body {
    font-family: "CustomFont", sans-serif;
}
```

3. **Update Tailwind config**:

```js
theme: {
    extend: {
        fontFamily: {
            sans: ['CustomFont', 'sans-serif'],
        },
    },
}
```

## Best Practices

### Version Control

-   Keep customizations in separate branches
-   Document all changes in commit messages
-   Use feature branches for major customizations

### Testing

-   Test all customizations in development environment
-   Verify responsive design on multiple devices
-   Test with different user roles

### Performance

-   Optimize images before adding to landing page
-   Minimize custom CSS and JavaScript
-   Use lazy loading for images

### Maintenance

-   Keep user guide content up to date
-   Review and update landing page content regularly
-   Monitor user feedback for improvement opportunities

## Support

For questions about customization, contact the development team or refer to:

-   Laravel Documentation: https://laravel.com/docs
-   React Documentation: https://react.dev
-   Tailwind CSS Documentation: https://tailwindcss.com/docs
-   Inertia.js Documentation: https://inertiajs.com

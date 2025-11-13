# Error Handling Documentation

This document describes the comprehensive error handling system implemented in the Concept Paper Tracker application.

## Overview

The application implements a multi-layered error handling approach:

1. **React Error Boundaries** - Catch JavaScript errors in components
2. **HTTP Error Handler** - Handle API/server errors (401, 403, 404, 419, 429, 500+)
3. **Validation Errors** - Display form validation errors inline and as summaries
4. **Toast Notifications** - Show success/error messages to users
5. **Accessibility** - Screen reader support and keyboard navigation

## Components

### 1. ErrorBoundary Component

**Location:** `resources/js/Components/ErrorBoundary.jsx`

**Purpose:** Catches JavaScript errors anywhere in the component tree and displays a fallback UI.

**Features:**

-   Catches rendering errors, lifecycle errors, and constructor errors
-   Shows user-friendly error message
-   Displays detailed error info in development mode
-   Provides "Try Again" and "Go to Dashboard" actions
-   Logs errors to console (can be extended to external services)

**Usage:**

```jsx
import ErrorBoundary from "@/Components/ErrorBoundary";

<ErrorBoundary>
    <YourComponent />
</ErrorBoundary>;
```

**Note:** Already integrated in:

-   Root app level (`app.jsx`)
-   `AuthenticatedLayout`
-   `GuestLayout`

### 2. Toast Component

**Location:** `resources/js/Components/Toast.jsx`

**Purpose:** Displays temporary notification messages for success, error, warning, and info.

**Features:**

-   Auto-dismisses after 5 seconds
-   Supports 4 types: success, error, warning, info
-   Animated slide-in from right
-   Accessible with ARIA attributes
-   Manual dismiss button

**Usage:**

```jsx
// Backend (Laravel Controller)
return redirect()->back()->with('success', 'Paper submitted successfully!');
return redirect()->back()->with('error', 'Failed to submit paper.');
return redirect()->back()->with('warning', 'This action cannot be undone.');
return redirect()->back()->with('info', 'Your session will expire in 5 minutes.');

// Frontend - Toast automatically picks up flash messages
// No additional code needed in components
```

**Note:** Already integrated in:

-   `AuthenticatedLayout`
-   `GuestLayout`

### 3. ValidationErrors Component

**Location:** `resources/js/Components/ValidationErrors.jsx`

**Purpose:** Displays a summary of all validation errors at the top of forms.

**Features:**

-   Shows count of errors
-   Lists all error messages
-   Auto-scrolls to errors when they appear
-   Accessible with ARIA live regions
-   Keyboard focusable for screen readers

**Usage:**

```jsx
import ValidationErrors from "@/Components/ValidationErrors";
import { useForm } from "@inertiajs/react";

const { errors } = useForm({
    /* ... */
});

<ValidationErrors errors={errors} className="mb-6" />;
```

### 4. InputError Component

**Location:** `resources/js/Components/InputError.jsx`

**Purpose:** Displays inline validation errors for individual form fields.

**Features:**

-   Shows error message below input field
-   Red text styling
-   Only renders when error exists

**Usage:**

```jsx
import InputError from '@/Components/InputError';
import { useForm } from '@inertiajs/react';

const { errors } = useForm({ /* ... */ });

<TextInput id="email" /* ... */ />
<InputError message={errors.email} className="mt-2" />
```

## Hooks

### 1. useHttpErrorHandler Hook

**Location:** `resources/js/Hooks/useHttpErrorHandler.js`

**Purpose:** Globally handles HTTP errors from API requests.

**Handles:**

-   **401 Unauthorized** - Redirects to login with return URL
-   **403 Forbidden** - Redirects to dashboard with error message
-   **404 Not Found** - Redirects to dashboard with error message
-   **419 CSRF Token Mismatch** - Reloads the page
-   **429 Too Many Requests** - Shows warning message
-   **500+ Server Errors** - Logs error and shows generic message

**Usage:**

```jsx
import useHttpErrorHandler from "@/Hooks/useHttpErrorHandler";

export default function MyLayout({ children }) {
    useHttpErrorHandler(); // Call in layout component

    return <div>{children}</div>;
}
```

**Note:** Already integrated in `AuthenticatedLayout`.

### 2. useToast Hook

**Location:** `resources/js/Hooks/useToast.js`

**Purpose:** Programmatically trigger toast notifications from components.

**Usage:**

```jsx
import useToast from "@/Hooks/useToast";

export default function MyComponent() {
    const toast = useToast();

    const handleAction = () => {
        // Show success message
        toast.success("Action completed successfully!");

        // Show error message
        toast.error("Something went wrong!");

        // Show warning message
        toast.warning("Please review your input.");

        // Show info message
        toast.info("Processing may take a few minutes.");
    };

    return <button onClick={handleAction}>Do Something</button>;
}
```

## Best Practices

### Form Validation

Always display both summary and inline errors:

```jsx
import ValidationErrors from "@/Components/ValidationErrors";
import InputError from "@/Components/InputError";
import { useForm } from "@inertiajs/react";

export default function MyForm() {
    const { data, setData, post, errors } = useForm({
        email: "",
        password: "",
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("my.route"));
    };

    return (
        <form onSubmit={handleSubmit}>
            {/* Summary at top */}
            <ValidationErrors errors={errors} className="mb-6" />

            {/* Inline errors per field */}
            <div>
                <TextInput
                    id="email"
                    value={data.email}
                    onChange={(e) => setData("email", e.target.value)}
                />
                <InputError message={errors.email} className="mt-2" />
            </div>

            <div>
                <TextInput
                    id="password"
                    type="password"
                    value={data.password}
                    onChange={(e) => setData("password", e.target.value)}
                />
                <InputError message={errors.password} className="mt-2" />
            </div>

            <button type="submit">Submit</button>
        </form>
    );
}
```

### Backend Flash Messages

Use Laravel's flash messages for user feedback:

```php
// Success
return redirect()->route('dashboard')
    ->with('success', 'Concept paper submitted successfully!');

// Error
return redirect()->back()
    ->with('error', 'Failed to upload file. Please try again.');

// Warning
return redirect()->back()
    ->with('warning', 'This action cannot be undone.');

// Info
return redirect()->back()
    ->with('info', 'Your changes have been saved as draft.');
```

### Error Boundaries

Wrap risky components in error boundaries:

```jsx
import ErrorBoundary from "@/Components/ErrorBoundary";

export default function MyPage() {
    return (
        <div>
            <SafeComponent />

            <ErrorBoundary>
                <RiskyComponent />
            </ErrorBoundary>

            <AnotherSafeComponent />
        </div>
    );
}
```

### Accessibility

Ensure all error messages are accessible:

```jsx
// Use semantic HTML
<div role="alert" aria-live="assertive">
    {errorMessage}
</div>

// Associate errors with inputs
<label htmlFor="email">Email</label>
<input
    id="email"
    aria-describedby="email-error"
    aria-invalid={errors.email ? "true" : "false"}
/>
<span id="email-error" role="alert">
    {errors.email}
</span>
```

## Testing Error Handling

### Test 401 Unauthorized

1. Log out
2. Try to access a protected route
3. Should redirect to login

### Test 403 Forbidden

1. Log in as requisitioner
2. Try to access admin route
3. Should redirect to dashboard with error

### Test Validation Errors

1. Submit a form with invalid data
2. Should see summary at top
3. Should see inline errors per field
4. Should auto-scroll to errors

### Test Toast Notifications

1. Submit a form successfully
2. Should see green success toast
3. Toast should auto-dismiss after 5 seconds

### Test Error Boundary

1. Trigger a JavaScript error in a component
2. Should see error boundary fallback UI
3. Should be able to recover with "Try Again"

## Troubleshooting

### Toast not showing

-   Check if `Toast` component is in layout
-   Verify flash message is set in controller
-   Check browser console for errors

### Validation errors not displaying

-   Verify `errors` prop is passed from `useForm`
-   Check if `ValidationErrors` component is rendered
-   Ensure error keys match form field names

### Error boundary not catching errors

-   Error boundaries only catch errors in child components
-   They don't catch errors in event handlers (use try-catch)
-   They don't catch async errors (use error states)

### HTTP errors not handled

-   Verify `useHttpErrorHandler` is called in layout
-   Check if Inertia error events are firing
-   Review browser network tab for response status

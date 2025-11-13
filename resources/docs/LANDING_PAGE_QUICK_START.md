# Landing Page Styling - Quick Start Guide

## Quick Reference

### 1. Using Custom Animations

```jsx
// Fade in with upward motion
<div className="animate-fade-in-up">Content</div>

// Staggered animations
<div className="animate-fade-in-up animation-delay-200">Content</div>
<div className="animate-fade-in-up animation-delay-400">Content</div>

// Floating effect
<div className="animate-float">Floating element</div>
```

### 2. Using Icons

```jsx
import { WorkflowIcon, CheckmarkIcon } from '@/Components/Landing/Icons';

<WorkflowIcon className="h-6 w-6 text-indigo-600" />
<CheckmarkIcon className="h-5 w-5 text-green-500" />
```

### 3. Using Responsive Containers

```jsx
import ResponsiveContainer, { Section, Card } from '@/Components/Landing/ResponsiveContainer';

// Container with max-width
<ResponsiveContainer size="narrow" padding="large">
  <h1>Content</h1>
</ResponsiveContainer>

// Section with background
<Section background="gray" padding="default" id="features">
  <h2>Features</h2>
</Section>

// Card with hover effect
<Card hover padding="default">
  <h3>Card Title</h3>
  <p>Card content</p>
</Card>
```

### 4. Using Scroll Animations

```jsx
import { useScrollAnimation } from "@/hooks/useScrollAnimation";

function MyComponent() {
    const { elementRef, isVisible } = useScrollAnimation({
        threshold: 0.2,
        animationClass: "animate-fade-in-up",
    });

    return <div ref={elementRef}>Animated content</div>;
}
```

### 5. Responsive Grid

```jsx
import { ResponsiveGrid } from "@/Components/Landing/ResponsiveContainer";

<ResponsiveGrid cols={{ sm: 1, md: 2, lg: 3 }} gap="large">
    {items.map((item) => (
        <Card key={item.id}>{item.content}</Card>
    ))}
</ResponsiveGrid>;
```

### 6. Custom Gradients

```css
/* In your component */
<div className="bg-gradient-primary text-white">
  Gradient background
</div>

/* Or use Tailwind's gradient utilities */
<div className="bg-gradient-to-r from-indigo-600 to-purple-600">
  Custom gradient
</div>
```

### 7. Badges

```jsx
import { Badge } from '@/Components/Landing/ResponsiveContainer';

<Badge variant="primary" size="default">New</Badge>
<Badge variant="success">Completed</Badge>
<Badge variant="warning">Pending</Badge>
```

### 8. Responsive Text

```jsx
import { ResponsiveText } from '@/Components/Landing/ResponsiveContainer';

<ResponsiveText variant="h1" align="center" color="default">
  Main Heading
</ResponsiveText>

<ResponsiveText variant="lead" color="muted">
  Subtitle text
</ResponsiveText>
```

### 9. Smooth Scrolling

```jsx
import { useSmoothScroll } from "@/hooks/useScrollAnimation";

function Navigation() {
    const { scrollToElement } = useSmoothScroll();

    return (
        <button onClick={() => scrollToElement("features", 80)}>
            View Features
        </button>
    );
}
```

### 10. Image Optimization

```jsx
import { lazyLoadImages, optimizeImage } from "@/utils/imageOptimization";

// In useEffect
useEffect(() => {
    lazyLoadImages("img[data-src]");
}, []);

// For individual images
<img
    data-src="/images/hero.jpg"
    alt="Hero"
    loading="lazy"
    decoding="async"
    className="lazy"
/>;
```

## Common Patterns

### Hero Section

```jsx
<Section background="gradient" padding="large">
    <ResponsiveContainer size="narrow">
        <ResponsiveText variant="h1" align="center">
            Your Hero Title
        </ResponsiveText>
        <ResponsiveText variant="lead" align="center" color="muted">
            Your subtitle
        </ResponsiveText>
    </ResponsiveContainer>
</Section>
```

### Feature Cards

```jsx
<Section background="gray">
    <ResponsiveContainer>
        <ResponsiveText variant="h2" align="center">
            Features
        </ResponsiveText>
        <ResponsiveGrid cols={{ sm: 1, md: 2, lg: 3 }}>
            {features.map((feature) => (
                <Card key={feature.id} hover>
                    <WorkflowIcon className="h-8 w-8 text-indigo-600 mb-4" />
                    <h3 className="text-lg font-semibold mb-2">
                        {feature.title}
                    </h3>
                    <p className="text-gray-600">{feature.description}</p>
                </Card>
            ))}
        </ResponsiveGrid>
    </ResponsiveContainer>
</Section>
```

### CTA Section

```jsx
<Section background="primary" padding="large">
    <ResponsiveContainer size="narrow">
        <ResponsiveText variant="h2" align="center" color="white">
            Ready to Get Started?
        </ResponsiveText>
        <div className="flex justify-center mt-8">
            <Link href="/register" className="btn-primary-landing">
                Sign Up Now
            </Link>
        </div>
    </ResponsiveContainer>
</Section>
```

## Tailwind Utility Classes

### Spacing

-   `p-4`, `p-6`, `p-8` - Padding
-   `m-4`, `m-6`, `m-8` - Margin
-   `space-x-4`, `space-y-4` - Gap between children

### Colors

-   `text-indigo-600` - Primary color
-   `bg-gray-50` - Light background
-   `border-gray-200` - Border color

### Typography

-   `text-4xl font-bold` - Large heading
-   `text-lg text-gray-600` - Body text
-   `font-semibold` - Semi-bold weight

### Layout

-   `flex items-center justify-between` - Flexbox
-   `grid grid-cols-3 gap-6` - Grid layout
-   `container mx-auto` - Centered container

### Effects

-   `shadow-sm`, `shadow-md`, `shadow-lg` - Shadows
-   `rounded-lg`, `rounded-xl` - Border radius
-   `hover:bg-indigo-700` - Hover states
-   `transition-all duration-300` - Smooth transitions

## Performance Tips

1. **Lazy load images below the fold**

    ```jsx
    <img data-src="/image.jpg" loading="lazy" className="lazy" />
    ```

2. **Use animation delays for staggered effects**

    ```jsx
    <div className="animate-fade-in-up animation-delay-200">Item 1</div>
    <div className="animate-fade-in-up animation-delay-400">Item 2</div>
    ```

3. **Optimize scroll handlers**

    - Use Intersection Observer (built into hooks)
    - Debounce scroll events
    - Use `passive: true` for event listeners

4. **Minimize layout shifts**
    - Set explicit dimensions for images
    - Use aspect ratio utilities
    - Reserve space for dynamic content

## Accessibility Checklist

-   [ ] All interactive elements have focus indicators
-   [ ] Icons have `aria-hidden="true"`
-   [ ] Images have descriptive alt text
-   [ ] Color contrast meets WCAG AA standards
-   [ ] Keyboard navigation works
-   [ ] Reduced motion preference respected

## Browser Testing

Test on:

-   Chrome (latest)
-   Firefox (latest)
-   Safari (latest)
-   Edge (latest)
-   Mobile browsers (iOS Safari, Chrome Mobile)

## Need Help?

-   Check full documentation: `resources/docs/LANDING_PAGE_STYLING.md`
-   Review component source code
-   Test in browser DevTools
-   Contact development team

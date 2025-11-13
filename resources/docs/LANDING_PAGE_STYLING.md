# Landing Page Styling and Assets Documentation

## Overview

This document describes the custom styling, assets, and responsive design implementation for the Concept Paper Tracker landing page.

## File Structure

```
resources/
├── css/
│   ├── app.css                 # Main CSS file with Tailwind imports
│   └── landing.css             # Custom landing page styles
├── js/
│   ├── Components/
│   │   └── Landing/
│   │       ├── Icons.jsx       # Custom SVG icon components
│   │       └── ResponsiveContainer.jsx  # Responsive layout components
│   ├── hooks/
│   │   └── useScrollAnimation.js  # Scroll animation hooks
│   └── utils/
│       └── imageOptimization.js   # Image optimization utilities
└── docs/
    └── LANDING_PAGE_STYLING.md    # This file
```

## Custom CSS Classes

### Animation Classes

#### Fade Animations

-   `.animate-fade-in-up` - Fade in with upward motion
-   `.animate-fade-in` - Simple fade in
-   `.animate-slide-in-left` - Slide in from left
-   `.animate-slide-in-right` - Slide in from right
-   `.animate-pulse-slow` - Slow pulsing effect
-   `.animate-float` - Floating animation

#### Animation Delays

-   `.animation-delay-100` through `.animation-delay-600` - Stagger animations

### Gradient Backgrounds

-   `.gradient-primary` - Indigo to purple gradient
-   `.gradient-secondary` - Pink to red gradient
-   `.gradient-success` - Blue to cyan gradient
-   `.gradient-overlay` - Subtle dark overlay

### Card Effects

-   `.card-hover` - Smooth hover effect with lift and shadow

### Button Styles

-   `.btn-primary-landing` - Primary CTA button
-   `.btn-secondary-landing` - Secondary button

### Icon Containers

-   `.icon-container` - Base icon wrapper
-   `.icon-container-primary` - Primary colored icon container

### Workflow Styles

-   `.workflow-connector` - Connects workflow stages
-   `.timeline-item` - Timeline item wrapper
-   `.timeline-dot` - Numbered circle for timeline
-   `.timeline-line` - Connecting line between stages

### Text Utilities

-   `.text-gradient` - Primary gradient text
-   `.text-gradient-success` - Success gradient text
-   `.truncate-2-lines` - Truncate text to 2 lines
-   `.truncate-3-lines` - Truncate text to 3 lines

### Responsive Typography

-   `.hero-title` - Responsive hero heading
-   `.hero-subtitle` - Responsive hero subtitle
-   `.section-title` - Responsive section heading

### Shadows

-   `.shadow-soft` - Subtle shadow
-   `.shadow-strong` - Prominent shadow

### Badges

-   `.badge-primary` - Primary badge
-   `.badge-success` - Success badge
-   `.badge-warning` - Warning badge

## Tailwind Configuration Extensions

### Custom Colors

```javascript
primary: {
  50: '#eef2ff',
  100: '#e0e7ff',
  // ... through 900
}
```

### Custom Animations

-   `fade-in-up` - 0.6s ease-out
-   `fade-in` - 0.8s ease-out
-   `slide-in-left` - 0.6s ease-out
-   `slide-in-right` - 0.6s ease-out
-   `pulse-slow` - 3s infinite
-   `float` - 3s infinite

### Custom Shadows

-   `soft` - Subtle elevation
-   `strong` - Prominent elevation

### Custom Background Images

-   `gradient-primary`
-   `gradient-secondary`
-   `gradient-success`

## Icon Components

### Available Icons

All icons are optimized SVG components with accessibility support:

-   `WorkflowIcon` - Workflow/process icon
-   `TrackingIcon` - Tracking/checkmark icon
-   `NotificationIcon` - Bell notification icon
-   `ClockIcon` - Time/deadline icon
-   `ReportIcon` - Chart/report icon
-   `SecurityIcon` - Shield/security icon
-   `CheckmarkIcon` - Simple checkmark
-   `ArrowDownIcon` - Down arrow
-   `SparklesIcon` - Sparkles/new feature
-   `LightningIcon` - Speed/fast icon
-   `UsersIcon` - Multiple users icon
-   `GlobeIcon` - Global/world icon
-   `TrendingUpIcon` - Growth/trending icon

### Decorative Patterns

-   `PatternDots` - Dot pattern background
-   `WavePattern` - Wave decoration

### Usage Example

```jsx
import { WorkflowIcon, CheckmarkIcon } from "@/Components/Landing/Icons";

<WorkflowIcon className="h-6 w-6 text-indigo-600" />;
```

## Responsive Container Components

### ResponsiveContainer

Main container with consistent max-width and padding.

**Props:**

-   `size`: 'narrow' | 'default' | 'wide' | 'full'
-   `padding`: 'none' | 'small' | 'default' | 'large'
-   `as`: HTML element type (default: 'div')

**Example:**

```jsx
<ResponsiveContainer size="narrow" padding="large">
    Content here
</ResponsiveContainer>
```

### Section

Section wrapper with background and padding variants.

**Props:**

-   `background`: 'white' | 'gray' | 'gradient' | 'primary' | 'dark'
-   `padding`: 'none' | 'small' | 'default' | 'large'
-   `id`: Section ID for anchor links

**Example:**

```jsx
<Section background="gray" padding="large" id="features">
    Section content
</Section>
```

### ResponsiveGrid

Responsive grid with configurable columns.

**Props:**

-   `cols`: Object with breakpoint columns `{ sm: 1, md: 2, lg: 3 }`
-   `gap`: 'none' | 'small' | 'default' | 'large'

**Example:**

```jsx
<ResponsiveGrid cols={{ sm: 1, md: 2, lg: 3 }} gap="large">
    {items.map((item) => (
        <Card key={item.id}>{item.content}</Card>
    ))}
</ResponsiveGrid>
```

### ResponsiveFlex

Flexible container with responsive direction.

**Props:**

-   `direction`: 'row' | 'row-reverse' | 'col' | 'col-reverse'
-   `align`: 'start' | 'center' | 'end' | 'stretch'
-   `justify`: 'start' | 'center' | 'end' | 'between' | 'around'
-   `gap`: 'none' | 'small' | 'default' | 'large'
-   `wrap`: boolean

### ResponsiveText

Text component with responsive sizing.

**Props:**

-   `variant`: 'h1' | 'h2' | 'h3' | 'h4' | 'lead' | 'body' | 'small'
-   `align`: 'left' | 'center' | 'right'
-   `color`: 'default' | 'muted' | 'light' | 'white' | 'primary'
-   `as`: HTML element type

### Card

Card component with hover effects.

**Props:**

-   `hover`: boolean (default: true)
-   `padding`: 'none' | 'small' | 'default' | 'large'

### Badge

Badge component for labels and tags.

**Props:**

-   `variant`: 'primary' | 'success' | 'warning' | 'danger' | 'gray'
-   `size`: 'small' | 'default' | 'large'

## Scroll Animation Hooks

### useScrollAnimation

Triggers animations when elements enter viewport.

**Options:**

-   `threshold`: Intersection threshold (0-1)
-   `rootMargin`: Margin around viewport
-   `triggerOnce`: Animate only once (default: true)
-   `animationClass`: CSS class to add

**Example:**

```jsx
const { elementRef, isVisible } = useScrollAnimation({
    threshold: 0.2,
    animationClass: "animate-fade-in-up",
});

<div ref={elementRef}>Content</div>;
```

### useParallax

Creates parallax scroll effect.

**Example:**

```jsx
const { elementRef, offset } = useParallax(0.5);

<div ref={elementRef} style={{ transform: `translateY(${offset}px)` }}>
    Parallax content
</div>;
```

### useScrollProgress

Tracks scroll progress as percentage.

**Example:**

```jsx
const progress = useScrollProgress();

<div style={{ width: `${progress}%` }} className="progress-bar" />;
```

### useScrollDirection

Detects scroll direction ('up' or 'down').

**Example:**

```jsx
const scrollDirection = useScrollDirection();

<header className={scrollDirection === "down" ? "hidden" : "visible"}>
    Header content
</header>;
```

### useSmoothScroll

Provides smooth scroll functions.

**Example:**

```jsx
const { scrollToElement, scrollToTop } = useSmoothScroll();

<button onClick={() => scrollToElement("features", 80)}>View Features</button>;
```

### useStickyHeader

Detects when header should be sticky.

**Example:**

```jsx
const isSticky = useStickyHeader(100);

<header className={isSticky ? "sticky shadow-lg" : ""}>Header content</header>;
```

### useInViewport

Detects if element is in viewport.

**Example:**

```jsx
const { elementRef, isInViewport } = useInViewport({ threshold: 0.5 });

<div ref={elementRef}>{isInViewport && <AnimatedContent />}</div>;
```

### useStaggeredAnimation

Animates items with stagger effect.

**Example:**

```jsx
const { containerRef, visibleItems } = useStaggeredAnimation(6, 100);

<div ref={containerRef}>
    {items.map((item, i) => (
        <div className={visibleItems.includes(i) ? "visible" : "hidden"}>
            {item}
        </div>
    ))}
</div>;
```

### useCountUp

Animates numbers counting up.

**Example:**

```jsx
const { elementRef, count } = useCountUp(1000, 2000, { decimals: 0 });

<div ref={elementRef}>
    <span className="stat-number">{count}</span>
</div>;
```

## Image Optimization Utilities

### generateSrcSet

Creates responsive image srcset.

```javascript
const srcset = generateSrcSet("/images", "hero", "jpg", [640, 1024, 1920]);
```

### lazyLoadImages

Implements lazy loading for images.

```javascript
useEffect(() => {
    lazyLoadImages("img[data-src]");
}, []);
```

### preloadImages

Preloads critical images.

```javascript
preloadImages(["/images/hero.jpg", "/images/logo.png"]);
```

### optimizeImage

Optimizes image loading attributes.

```javascript
optimizeImage(imgElement, {
    loading: "lazy",
    decoding: "async",
    fetchpriority: "high",
});
```

## Responsive Design Breakpoints

### Tailwind Breakpoints

-   `sm`: 640px
-   `md`: 768px
-   `lg`: 1024px
-   `xl`: 1280px
-   `2xl`: 1536px

### Mobile-First Approach

All styles are mobile-first. Use breakpoint prefixes for larger screens:

```jsx
<div className="text-base md:text-lg lg:text-xl">Responsive text</div>
```

## Accessibility Features

### Focus Indicators

All interactive elements have visible focus indicators:

```css
*:focus-visible {
    outline: 2px solid theme("colors.indigo.500");
    outline-offset: 2px;
}
```

### Touch Targets

Minimum 44x44px touch targets on mobile devices.

### Screen Reader Support

-   All icons have `aria-hidden="true"`
-   Decorative elements are hidden from screen readers
-   Semantic HTML structure

### Reduced Motion

Respects `prefers-reduced-motion` preference:

```css
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        transition-duration: 0.01ms !important;
    }
}
```

## Performance Optimizations

### CSS

-   Minimal custom CSS, leveraging Tailwind utilities
-   Critical CSS inlined
-   Non-critical CSS deferred

### Images

-   Lazy loading for below-fold images
-   Responsive images with srcset
-   WebP format with fallbacks
-   Optimized SVG icons

### Animations

-   GPU-accelerated transforms
-   RequestAnimationFrame for smooth animations
-   Intersection Observer for scroll triggers

### JavaScript

-   Code splitting for landing page components
-   Lazy loading of non-critical components
-   Debounced scroll handlers

## Browser Support

### Modern Browsers

-   Chrome 90+
-   Firefox 88+
-   Safari 14+
-   Edge 90+

### Fallbacks

-   Intersection Observer polyfill for older browsers
-   CSS Grid fallback to Flexbox
-   Gradient fallback to solid colors

## Testing Checklist

### Responsive Design

-   [ ] Test on mobile (320px - 767px)
-   [ ] Test on tablet (768px - 1023px)
-   [ ] Test on desktop (1024px+)
-   [ ] Test on large screens (1920px+)

### Performance

-   [ ] Lighthouse score > 90
-   [ ] First Contentful Paint < 1.5s
-   [ ] Largest Contentful Paint < 2.5s
-   [ ] Cumulative Layout Shift < 0.1

### Accessibility

-   [ ] Keyboard navigation works
-   [ ] Screen reader compatible
-   [ ] Color contrast meets WCAG AA
-   [ ] Focus indicators visible

### Cross-Browser

-   [ ] Chrome
-   [ ] Firefox
-   [ ] Safari
-   [ ] Edge

## Maintenance

### Adding New Animations

1. Define keyframes in `landing.css`
2. Add animation class
3. Update Tailwind config if needed
4. Document in this file

### Adding New Icons

1. Create SVG component in `Icons.jsx`
2. Optimize SVG (remove unnecessary attributes)
3. Add `aria-hidden="true"`
4. Document in this file

### Updating Responsive Breakpoints

1. Update Tailwind config
2. Update component responsive classes
3. Test across all breakpoints
4. Update documentation

## Resources

-   [Tailwind CSS Documentation](https://tailwindcss.com/docs)
-   [Heroicons](https://heroicons.com/)
-   [Web.dev Performance](https://web.dev/performance/)
-   [WCAG Guidelines](https://www.w3.org/WAI/WCAG21/quickref/)

## Support

For questions or issues with landing page styling:

1. Check this documentation
2. Review component source code
3. Test in browser DevTools
4. Contact development team

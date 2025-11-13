# Landing Page Visual Reference Guide

## Color Palette

### Primary Colors (Indigo)

```
50:  #eef2ff - Very light indigo (backgrounds)
100: #e0e7ff - Light indigo (hover states)
200: #c7d2fe - Soft indigo (borders)
300: #a5b4fc - Medium light indigo
400: #818cf8 - Medium indigo
500: #6366f1 - Base indigo
600: #4f46e5 - Primary brand color ⭐
700: #4338ca - Dark indigo (hover)
800: #3730a3 - Darker indigo
900: #312e81 - Darkest indigo
```

### Secondary Colors (Purple)

```
600: #764ba2 - Secondary brand color
Used in gradients with indigo
```

### Neutral Colors (Gray)

```
50:  #f9fafb - Lightest gray (backgrounds)
100: #f3f4f6 - Very light gray (cards)
200: #e5e7eb - Light gray (borders)
300: #d1d5db - Medium light gray
400: #9ca3af - Medium gray
500: #6b7280 - Base gray
600: #4b5563 - Dark gray (text)
700: #374151 - Darker gray
800: #1f2937 - Very dark gray
900: #111827 - Darkest gray (headings)
```

### Semantic Colors

**Success (Green)**

```
100: #d1fae5 - Light green (badges)
500: #10b981 - Success green
600: #059669 - Dark success
800: #065f46 - Success text
```

**Warning (Yellow)**

```
100: #fef3c7 - Light yellow (badges)
500: #f59e0b - Warning yellow
800: #92400e - Warning text
```

**Danger (Red)**

```
100: #fee2e2 - Light red (badges)
500: #ef4444 - Danger red
800: #991b1b - Danger text
```

## Typography Scale

### Headings

```
H1: text-4xl sm:text-5xl md:text-6xl (36px → 48px → 60px)
H2: text-3xl sm:text-4xl md:text-5xl (30px → 36px → 48px)
H3: text-2xl sm:text-3xl md:text-4xl (24px → 30px → 36px)
H4: text-xl sm:text-2xl (20px → 24px)
```

### Body Text

```
Lead:  text-lg sm:text-xl md:text-2xl (18px → 20px → 24px)
Body:  text-base sm:text-lg (16px → 18px)
Small: text-sm sm:text-base (14px → 16px)
```

### Font Weights

```
font-normal:   400 - Regular text
font-medium:   500 - Emphasized text
font-semibold: 600 - Subheadings
font-bold:     700 - Headings
```

## Spacing Scale

### Padding/Margin

```
p-2:  0.5rem  (8px)
p-4:  1rem    (16px)
p-6:  1.5rem  (24px)
p-8:  2rem    (32px)
p-12: 3rem    (48px)
p-16: 4rem    (64px)
p-20: 5rem    (80px)
p-32: 8rem    (128px)
```

### Gap (Grid/Flex)

```
gap-2:  0.5rem  (8px)
gap-4:  1rem    (16px)
gap-6:  1.5rem  (24px)
gap-8:  2rem    (32px)
gap-12: 3rem    (48px)
```

## Border Radius

```
rounded:     0.25rem (4px)  - Small elements
rounded-md:  0.375rem (6px)  - Buttons
rounded-lg:  0.5rem (8px)   - Cards
rounded-xl:  0.75rem (12px)  - Large cards
rounded-2xl: 1rem (16px)    - Hero sections
rounded-full: 9999px        - Pills, badges, avatars
```

## Shadows

### Elevation Levels

```
shadow-sm:     Subtle elevation (cards at rest)
shadow:        Default elevation (buttons)
shadow-md:     Medium elevation (dropdowns)
shadow-lg:     High elevation (modals)
shadow-xl:     Very high elevation (popovers)
shadow-2xl:    Maximum elevation (overlays)

Custom:
shadow-soft:   Soft, diffused shadow
shadow-strong: Prominent, defined shadow
```

## Component Patterns

### Button Styles

**Primary Button**

```jsx
<button className="px-8 py-4 bg-indigo-600 text-white text-lg font-semibold rounded-lg hover:bg-indigo-700 transition-all duration-300 shadow-lg hover:shadow-xl transform hover:-translate-y-0.5">
    Primary Action
</button>
```

**Secondary Button**

```jsx
<button className="px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-lg border-2 border-indigo-600 hover:bg-indigo-50 transition-all duration-300">
    Secondary Action
</button>
```

### Card Styles

**Basic Card**

```jsx
<div className="p-6 bg-white rounded-xl shadow-sm border border-gray-100">
    Card content
</div>
```

**Hover Card**

```jsx
<div className="p-6 bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow hover:-translate-y-1 transition-transform">
    Interactive card
</div>
```

**Gradient Card Header**

```jsx
<div className="bg-white rounded-xl shadow-sm overflow-hidden">
    <div className="p-6 bg-gradient-to-r from-indigo-50 to-purple-50">
        Header content
    </div>
    <div className="p-6">Body content</div>
</div>
```

### Badge Styles

```jsx
<!-- Primary -->
<span className="px-3 py-1 bg-indigo-100 text-indigo-800 text-sm font-medium rounded-full">
  Primary
</span>

<!-- Success -->
<span className="px-3 py-1 bg-green-100 text-green-800 text-sm font-medium rounded-full">
  Success
</span>

<!-- Warning -->
<span className="px-3 py-1 bg-yellow-100 text-yellow-800 text-sm font-medium rounded-full">
  Warning
</span>
```

### Icon Container Styles

```jsx
<!-- Primary -->
<div className="p-3 bg-indigo-100 rounded-lg">
  <Icon className="h-6 w-6 text-indigo-600" />
</div>

<!-- Success -->
<div className="p-3 bg-green-100 rounded-lg">
  <Icon className="h-6 w-6 text-green-600" />
</div>

<!-- With hover effect -->
<div className="p-3 bg-indigo-100 rounded-lg hover:bg-indigo-200 hover:scale-110 transition-all">
  <Icon className="h-6 w-6 text-indigo-600" />
</div>
```

## Layout Patterns

### Section Spacing

```jsx
<!-- Default section -->
<section className="py-20">
  Content
</section>

<!-- Large section -->
<section className="py-32">
  Content
</section>

<!-- With background -->
<section className="py-20 bg-gray-50">
  Content
</section>
```

### Container Widths

```jsx
<!-- Narrow (reading content) -->
<div className="max-w-4xl mx-auto px-6">
  Content
</div>

<!-- Default (most sections) -->
<div className="max-w-7xl mx-auto px-6">
  Content
</div>

<!-- Wide (full-width sections) -->
<div className="max-w-[1400px] mx-auto px-6">
  Content
</div>
```

### Grid Layouts

```jsx
<!-- 3-column grid -->
<div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
  {items.map(item => <Card key={item.id}>{item}</Card>)}
</div>

<!-- Auto-fit grid -->
<div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
  Content
</div>

<!-- Responsive gap -->
<div className="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 lg:gap-8">
  Content
</div>
```

## Animation Patterns

### Fade In Animations

```jsx
<!-- Simple fade in -->
<div className="animate-fade-in">
  Content
</div>

<!-- Fade in with upward motion -->
<div className="animate-fade-in-up">
  Content
</div>

<!-- Staggered animations -->
<div className="animate-fade-in-up animation-delay-100">Item 1</div>
<div className="animate-fade-in-up animation-delay-200">Item 2</div>
<div className="animate-fade-in-up animation-delay-300">Item 3</div>
```

### Hover Animations

```jsx
<!-- Scale on hover -->
<div className="transition-transform hover:scale-105">
  Content
</div>

<!-- Lift on hover -->
<div className="transition-all hover:-translate-y-1 hover:shadow-lg">
  Content
</div>

<!-- Glow on hover -->
<button className="transition-all hover:shadow-xl hover:shadow-indigo-500/50">
  Button
</button>
```

### Loading States

```jsx
<!-- Pulse animation -->
<div className="animate-pulse bg-gray-200 h-4 rounded">
  Loading...
</div>

<!-- Slow pulse -->
<div className="animate-pulse-slow">
  Content
</div>

<!-- Floating animation -->
<div className="animate-float">
  Floating element
</div>
```

## Gradient Patterns

### Background Gradients

```jsx
<!-- Primary gradient -->
<div className="bg-gradient-to-r from-indigo-600 to-purple-600">
  Content
</div>

<!-- Subtle gradient -->
<div className="bg-gradient-to-b from-gray-50 to-white">
  Content
</div>

<!-- Overlay gradient -->
<div className="relative">
  <img src="background.jpg" />
  <div className="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent">
    Content
  </div>
</div>
```

### Text Gradients

```jsx
<!-- Primary gradient text -->
<h1 className="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
  Gradient Text
</h1>

<!-- Success gradient text -->
<span className="text-transparent bg-clip-text bg-gradient-to-r from-green-600 to-emerald-600">
  Success Text
</span>
```

## Responsive Breakpoints

### Mobile First Approach

```jsx
<!-- Base: Mobile (< 640px) -->
<div className="text-base">

<!-- Small: Tablet (≥ 640px) -->
<div className="text-base sm:text-lg">

<!-- Medium: Small Desktop (≥ 768px) -->
<div className="text-base sm:text-lg md:text-xl">

<!-- Large: Desktop (≥ 1024px) -->
<div className="text-base sm:text-lg md:text-xl lg:text-2xl">

<!-- Extra Large: Large Desktop (≥ 1280px) -->
<div className="text-base sm:text-lg md:text-xl lg:text-2xl xl:text-3xl">
```

### Common Responsive Patterns

```jsx
<!-- Stack on mobile, row on desktop -->
<div className="flex flex-col md:flex-row gap-4">
  Content
</div>

<!-- Hide on mobile, show on desktop -->
<div className="hidden md:block">
  Desktop only
</div>

<!-- Show on mobile, hide on desktop -->
<div className="block md:hidden">
  Mobile only
</div>

<!-- Different padding by breakpoint -->
<div className="px-4 sm:px-6 lg:px-8">
  Content
</div>
```

## Accessibility Patterns

### Focus States

```jsx
<!-- Visible focus ring -->
<button className="focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
  Button
</button>

<!-- Custom focus style -->
<a className="focus-visible:outline-2 focus-visible:outline-indigo-500">
  Link
</a>
```

### Screen Reader Text

```jsx
<!-- Visually hidden but accessible -->
<span className="sr-only">
  Screen reader only text
</span>

<!-- Skip to main content -->
<a href="#main" className="sr-only focus:not-sr-only">
  Skip to main content
</a>
```

### ARIA Labels

```jsx
<!-- Icon button with label -->
<button aria-label="Close menu">
  <XIcon className="h-6 w-6" aria-hidden="true" />
</button>

<!-- Decorative image -->
<img src="decoration.svg" alt="" role="presentation" />
```

## Print Styles

```jsx
<!-- Hide in print -->
<div className="no-print">
  Navigation, buttons, etc.
</div>

<!-- Print-specific styles -->
<div className="print:text-black print:bg-white">
  Content
</div>
```

## Dark Mode (Optional)

```jsx
<!-- Dark mode support -->
<div className="bg-white dark:bg-gray-900 text-gray-900 dark:text-white">
  Content
</div>

<!-- Dark mode gradient -->
<div className="bg-gradient-to-r from-indigo-600 to-purple-600 dark:from-indigo-800 dark:to-purple-800">
  Content
</div>
```

## Performance Optimization

### Image Loading

```jsx
<!-- Lazy load images -->
<img
  src="image.jpg"
  loading="lazy"
  decoding="async"
  alt="Description"
/>

<!-- Priority image (above fold) -->
<img
  src="hero.jpg"
  loading="eager"
  fetchpriority="high"
  alt="Hero"
/>
```

### Animation Performance

```jsx
<!-- Use transform instead of position -->
<div className="transition-transform hover:translate-y-1">
  <!-- Good: GPU accelerated -->
</div>

<div className="transition-all hover:top-1">
  <!-- Avoid: Causes reflow -->
</div>
```

## Quick Copy Templates

### Hero Section

```jsx
<section className="pt-32 pb-20 px-6 bg-gradient-to-b from-gray-50 to-white">
    <div className="container mx-auto text-center max-w-4xl">
        <h1 className="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
            Your Hero Title
        </h1>
        <p className="text-xl md:text-2xl text-gray-600 mb-8">
            Your compelling subtitle
        </p>
        <div className="flex flex-col sm:flex-row justify-center gap-4">
            <button className="btn-primary-landing">Get Started</button>
            <button className="btn-secondary-landing">Learn More</button>
        </div>
    </div>
</section>
```

### Feature Grid

```jsx
<section className="py-20 bg-gray-50">
    <div className="container mx-auto px-6">
        <h2 className="text-4xl font-bold text-center mb-12">Features</h2>
        <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            {features.map((feature) => (
                <div
                    key={feature.id}
                    className="p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow"
                >
                    <div className="p-3 bg-indigo-100 rounded-lg inline-block mb-4">
                        <Icon className="h-6 w-6 text-indigo-600" />
                    </div>
                    <h3 className="text-lg font-semibold mb-2">
                        {feature.title}
                    </h3>
                    <p className="text-gray-600">{feature.description}</p>
                </div>
            ))}
        </div>
    </div>
</section>
```

### CTA Section

```jsx
<section className="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
    <div className="container mx-auto px-6 text-center">
        <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
            Ready to Get Started?
        </h2>
        <p className="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
            Join thousands of users already using our platform
        </p>
        <button className="px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-lg hover:bg-gray-50 transition-colors shadow-lg">
            Sign Up Now
        </button>
    </div>
</section>
```

import { forwardRef } from "react";

/**
 * Responsive Container Component
 * Provides consistent responsive padding and max-width across landing page sections
 */
const ResponsiveContainer = forwardRef(
    (
        {
            children,
            className = "",
            size = "default",
            padding = "default",
            as: Component = "div",
            ...props
        },
        ref
    ) => {
        // Size variants
        const sizeClasses = {
            narrow: "max-w-4xl",
            default: "max-w-7xl",
            wide: "max-w-[1400px]",
            full: "max-w-full",
        };

        // Padding variants
        const paddingClasses = {
            none: "",
            small: "px-4 sm:px-6",
            default: "px-4 sm:px-6 lg:px-8",
            large: "px-6 sm:px-8 lg:px-12",
        };

        const containerClasses = `
        mx-auto
        ${sizeClasses[size] || sizeClasses.default}
        ${paddingClasses[padding] || paddingClasses.default}
        ${className}
    `
            .trim()
            .replace(/\s+/g, " ");

        return (
            <Component ref={ref} className={containerClasses} {...props}>
                {children}
            </Component>
        );
    }
);

ResponsiveContainer.displayName = "ResponsiveContainer";

export default ResponsiveContainer;

/**
 * Section Component with consistent spacing
 */
export function Section({
    children,
    className = "",
    background = "white",
    padding = "default",
    id,
    ...props
}) {
    const backgroundClasses = {
        white: "bg-white",
        gray: "bg-gray-50",
        gradient: "bg-gradient-to-b from-gray-50 to-white",
        primary: "bg-gradient-to-r from-indigo-600 to-purple-600",
        dark: "bg-gray-900",
    };

    const paddingClasses = {
        none: "",
        small: "py-12",
        default: "py-20",
        large: "py-32",
    };

    const sectionClasses = `
        ${backgroundClasses[background] || backgroundClasses.white}
        ${paddingClasses[padding] || paddingClasses.default}
        ${className}
    `
        .trim()
        .replace(/\s+/g, " ");

    return (
        <section id={id} className={sectionClasses} {...props}>
            {children}
        </section>
    );
}

/**
 * Grid Component with responsive columns
 */
export function ResponsiveGrid({
    children,
    className = "",
    cols = { sm: 1, md: 2, lg: 3 },
    gap = "default",
    ...props
}) {
    const gapClasses = {
        none: "gap-0",
        small: "gap-4",
        default: "gap-6 md:gap-8",
        large: "gap-8 md:gap-12",
    };

    const gridClasses = `
        grid
        grid-cols-${cols.sm || 1}
        ${cols.md ? `md:grid-cols-${cols.md}` : ""}
        ${cols.lg ? `lg:grid-cols-${cols.lg}` : ""}
        ${cols.xl ? `xl:grid-cols-${cols.xl}` : ""}
        ${gapClasses[gap] || gapClasses.default}
        ${className}
    `
        .trim()
        .replace(/\s+/g, " ");

    return (
        <div className={gridClasses} {...props}>
            {children}
        </div>
    );
}

/**
 * Flex Container with responsive direction
 */
export function ResponsiveFlex({
    children,
    className = "",
    direction = "row",
    align = "center",
    justify = "start",
    gap = "default",
    wrap = false,
    ...props
}) {
    const directionClasses = {
        row: "flex-col sm:flex-row",
        "row-reverse": "flex-col-reverse sm:flex-row-reverse",
        col: "flex-col",
        "col-reverse": "flex-col-reverse",
    };

    const alignClasses = {
        start: "items-start",
        center: "items-center",
        end: "items-end",
        stretch: "items-stretch",
    };

    const justifyClasses = {
        start: "justify-start",
        center: "justify-center",
        end: "justify-end",
        between: "justify-between",
        around: "justify-around",
    };

    const gapClasses = {
        none: "gap-0",
        small: "gap-2 sm:gap-4",
        default: "gap-4 sm:gap-6",
        large: "gap-6 sm:gap-8",
    };

    const flexClasses = `
        flex
        ${directionClasses[direction] || directionClasses.row}
        ${alignClasses[align] || alignClasses.center}
        ${justifyClasses[justify] || justifyClasses.start}
        ${gapClasses[gap] || gapClasses.default}
        ${wrap ? "flex-wrap" : ""}
        ${className}
    `
        .trim()
        .replace(/\s+/g, " ");

    return (
        <div className={flexClasses} {...props}>
            {children}
        </div>
    );
}

/**
 * Responsive Text Component
 */
export function ResponsiveText({
    children,
    className = "",
    variant = "body",
    align = "left",
    color = "default",
    as: Component = "p",
    ...props
}) {
    const variantClasses = {
        h1: "text-4xl sm:text-5xl md:text-6xl font-bold",
        h2: "text-3xl sm:text-4xl md:text-5xl font-bold",
        h3: "text-2xl sm:text-3xl md:text-4xl font-bold",
        h4: "text-xl sm:text-2xl font-semibold",
        lead: "text-lg sm:text-xl md:text-2xl",
        body: "text-base sm:text-lg",
        small: "text-sm sm:text-base",
    };

    const alignClasses = {
        left: "text-left",
        center: "text-center",
        right: "text-right",
    };

    const colorClasses = {
        default: "text-gray-900",
        muted: "text-gray-600",
        light: "text-gray-500",
        white: "text-white",
        primary: "text-indigo-600",
    };

    const textClasses = `
        ${variantClasses[variant] || variantClasses.body}
        ${alignClasses[align] || alignClasses.left}
        ${colorClasses[color] || colorClasses.default}
        ${className}
    `
        .trim()
        .replace(/\s+/g, " ");

    return (
        <Component className={textClasses} {...props}>
            {children}
        </Component>
    );
}

/**
 * Card Component with hover effects
 */
export function Card({
    children,
    className = "",
    hover = true,
    padding = "default",
    ...props
}) {
    const paddingClasses = {
        none: "",
        small: "p-4",
        default: "p-6",
        large: "p-8",
    };

    const cardClasses = `
        bg-white
        rounded-xl
        shadow-sm
        border border-gray-100
        ${hover ? "card-hover" : ""}
        ${paddingClasses[padding] || paddingClasses.default}
        ${className}
    `
        .trim()
        .replace(/\s+/g, " ");

    return (
        <div className={cardClasses} {...props}>
            {children}
        </div>
    );
}

/**
 * Badge Component
 */
export function Badge({
    children,
    className = "",
    variant = "primary",
    size = "default",
    ...props
}) {
    const variantClasses = {
        primary: "bg-indigo-100 text-indigo-800",
        success: "bg-green-100 text-green-800",
        warning: "bg-yellow-100 text-yellow-800",
        danger: "bg-red-100 text-red-800",
        gray: "bg-gray-100 text-gray-800",
    };

    const sizeClasses = {
        small: "px-2 py-0.5 text-xs",
        default: "px-3 py-1 text-sm",
        large: "px-4 py-1.5 text-base",
    };

    const badgeClasses = `
        inline-flex
        items-center
        rounded-full
        font-medium
        ${variantClasses[variant] || variantClasses.primary}
        ${sizeClasses[size] || sizeClasses.default}
        ${className}
    `
        .trim()
        .replace(/\s+/g, " ");

    return (
        <span className={badgeClasses} {...props}>
            {children}
        </span>
    );
}

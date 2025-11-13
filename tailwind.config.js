import defaultTheme from "tailwindcss/defaultTheme";
import forms from "@tailwindcss/forms";
import typography from "@tailwindcss/typography";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
        "./storage/framework/views/*.php",
        "./resources/views/**/*.blade.php",
        "./resources/js/**/*.jsx",
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ["Figtree", ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: "#eef2ff",
                    100: "#e0e7ff",
                    200: "#c7d2fe",
                    300: "#a5b4fc",
                    400: "#818cf8",
                    500: "#6366f1",
                    600: "#4f46e5",
                    700: "#4338ca",
                    800: "#3730a3",
                    900: "#312e81",
                },
            },
            typography: (theme) => ({
                DEFAULT: {
                    css: {
                        maxWidth: "none",
                        color: theme("colors.gray.700"),
                        a: {
                            color: theme("colors.indigo.600"),
                            "&:hover": {
                                color: theme("colors.indigo.800"),
                            },
                            textDecoration: "underline",
                            fontWeight: "500",
                        },
                        strong: {
                            color: theme("colors.gray.900"),
                            fontWeight: "600",
                        },
                        "ol > li::marker": {
                            color: theme("colors.gray.500"),
                        },
                        "ul > li::marker": {
                            color: theme("colors.indigo.500"),
                        },
                        hr: {
                            borderColor: theme("colors.gray.200"),
                            marginTop: "2em",
                            marginBottom: "2em",
                        },
                        blockquote: {
                            fontWeight: "400",
                            fontStyle: "normal",
                            color: theme("colors.gray.700"),
                            borderLeftWidth: "0.25rem",
                            borderLeftColor: theme("colors.indigo.500"),
                            quotes: '"\\201C""\\201D""\\2018""\\2019"',
                            backgroundColor: theme("colors.indigo.50"),
                            padding: "1rem 1.5rem",
                            borderRadius: "0.375rem",
                        },
                        h1: {
                            color: theme("colors.gray.900"),
                            fontWeight: "700",
                            fontSize: "2.25em",
                            marginTop: "0",
                            marginBottom: "0.8888889em",
                            lineHeight: "1.1111111",
                        },
                        h2: {
                            color: theme("colors.gray.900"),
                            fontWeight: "600",
                            fontSize: "1.5em",
                            marginTop: "2em",
                            marginBottom: "1em",
                            lineHeight: "1.3333333",
                        },
                        h3: {
                            color: theme("colors.gray.900"),
                            fontWeight: "600",
                            fontSize: "1.25em",
                            marginTop: "1.6em",
                            marginBottom: "0.6em",
                            lineHeight: "1.6",
                        },
                        h4: {
                            color: theme("colors.gray.900"),
                            fontWeight: "600",
                            marginTop: "1.5em",
                            marginBottom: "0.5em",
                            lineHeight: "1.5",
                        },
                        code: {
                            color: theme("colors.indigo.600"),
                            backgroundColor: theme("colors.gray.100"),
                            paddingLeft: "0.375rem",
                            paddingRight: "0.375rem",
                            paddingTop: "0.125rem",
                            paddingBottom: "0.125rem",
                            borderRadius: "0.25rem",
                            fontWeight: "500",
                            fontSize: "0.875em",
                        },
                        "code::before": {
                            content: '""',
                        },
                        "code::after": {
                            content: '""',
                        },
                        pre: {
                            backgroundColor: theme("colors.gray.900"),
                            color: theme("colors.gray.100"),
                            overflowX: "auto",
                            fontSize: "0.875em",
                            lineHeight: "1.7142857",
                            marginTop: "1.7142857em",
                            marginBottom: "1.7142857em",
                            borderRadius: "0.5rem",
                            paddingTop: "1rem",
                            paddingRight: "1.5rem",
                            paddingBottom: "1rem",
                            paddingLeft: "1.5rem",
                        },
                        "pre code": {
                            backgroundColor: "transparent",
                            borderWidth: "0",
                            borderRadius: "0",
                            padding: "0",
                            fontWeight: "400",
                            color: "inherit",
                            fontSize: "inherit",
                            fontFamily: "inherit",
                            lineHeight: "inherit",
                        },
                        "pre code::before": {
                            content: "none",
                        },
                        "pre code::after": {
                            content: "none",
                        },
                        table: {
                            width: "100%",
                            tableLayout: "auto",
                            textAlign: "left",
                            marginTop: "2em",
                            marginBottom: "2em",
                            fontSize: "0.875em",
                            lineHeight: "1.7142857",
                        },
                        thead: {
                            borderBottomWidth: "2px",
                            borderBottomColor: theme("colors.gray.300"),
                        },
                        "thead th": {
                            color: theme("colors.gray.900"),
                            fontWeight: "600",
                            verticalAlign: "bottom",
                            paddingRight: "0.5714286em",
                            paddingBottom: "0.5714286em",
                            paddingLeft: "0.5714286em",
                        },
                        "tbody tr": {
                            borderBottomWidth: "1px",
                            borderBottomColor: theme("colors.gray.200"),
                        },
                        "tbody tr:last-child": {
                            borderBottomWidth: "0",
                        },
                        "tbody td": {
                            verticalAlign: "top",
                            paddingTop: "0.5714286em",
                            paddingRight: "0.5714286em",
                            paddingBottom: "0.5714286em",
                            paddingLeft: "0.5714286em",
                        },
                        img: {
                            marginTop: "2em",
                            marginBottom: "2em",
                            borderRadius: "0.5rem",
                        },
                        "ul > li": {
                            paddingLeft: "0.375em",
                        },
                        "ol > li": {
                            paddingLeft: "0.375em",
                        },
                    },
                },
                indigo: {
                    css: {
                        "--tw-prose-body": theme("colors.gray.700"),
                        "--tw-prose-headings": theme("colors.gray.900"),
                        "--tw-prose-lead": theme("colors.gray.600"),
                        "--tw-prose-links": theme("colors.indigo.600"),
                        "--tw-prose-bold": theme("colors.gray.900"),
                        "--tw-prose-counters": theme("colors.gray.500"),
                        "--tw-prose-bullets": theme("colors.indigo.500"),
                        "--tw-prose-hr": theme("colors.gray.200"),
                        "--tw-prose-quotes": theme("colors.gray.900"),
                        "--tw-prose-quote-borders": theme("colors.indigo.500"),
                        "--tw-prose-captions": theme("colors.gray.500"),
                        "--tw-prose-code": theme("colors.indigo.600"),
                        "--tw-prose-pre-code": theme("colors.gray.100"),
                        "--tw-prose-pre-bg": theme("colors.gray.900"),
                        "--tw-prose-th-borders": theme("colors.gray.300"),
                        "--tw-prose-td-borders": theme("colors.gray.200"),
                    },
                },
            }),
            animation: {
                "fade-in-up": "fadeInUp 0.6s ease-out forwards",
                "fade-in": "fadeIn 0.8s ease-out forwards",
                "slide-in-left": "slideInLeft 0.6s ease-out forwards",
                "slide-in-right": "slideInRight 0.6s ease-out forwards",
                "pulse-slow": "pulse 3s ease-in-out infinite",
                float: "float 3s ease-in-out infinite",
            },
            keyframes: {
                fadeInUp: {
                    "0%": { opacity: "0", transform: "translateY(30px)" },
                    "100%": { opacity: "1", transform: "translateY(0)" },
                },
                fadeIn: {
                    "0%": { opacity: "0" },
                    "100%": { opacity: "1" },
                },
                slideInLeft: {
                    "0%": { opacity: "0", transform: "translateX(-30px)" },
                    "100%": { opacity: "1", transform: "translateX(0)" },
                },
                slideInRight: {
                    "0%": { opacity: "0", transform: "translateX(30px)" },
                    "100%": { opacity: "1", transform: "translateX(0)" },
                },
                float: {
                    "0%, 100%": { transform: "translateY(0px)" },
                    "50%": { transform: "translateY(-10px)" },
                },
            },
            boxShadow: {
                soft: "0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)",
                strong: "0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)",
            },
            backgroundImage: {
                "gradient-primary":
                    "linear-gradient(135deg, #667eea 0%, #764ba2 100%)",
                "gradient-secondary":
                    "linear-gradient(135deg, #f093fb 0%, #f5576c 100%)",
                "gradient-success":
                    "linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)",
            },
        },
    },

    plugins: [forms, typography],
};

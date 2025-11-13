import { useEffect } from "react";
import { router } from "@inertiajs/react";

/**
 * Custom hook to handle HTTP errors globally
 * Handles 401 (Unauthorized), 403 (Forbidden), 404 (Not Found),
 * 419 (CSRF Token Mismatch), 429 (Too Many Requests), and 500+ (Server Errors)
 */
export default function useHttpErrorHandler() {
    useEffect(() => {
        const handleError = (event) => {
            const response = event.detail?.response;

            if (!response) return;

            const status = response.status;

            // Handle 401 Unauthorized - redirect to login with return URL
            if (status === 401) {
                const currentPath = window.location.pathname;
                router.visit("/login", {
                    method: "get",
                    data: {
                        redirect: currentPath !== "/login" ? currentPath : null,
                    },
                    onSuccess: () => {
                        console.log(
                            "Redirected to login due to unauthorized access"
                        );
                    },
                });
                return;
            }

            // Handle 403 Forbidden - show error and redirect to dashboard
            if (status === 403) {
                router.visit("/dashboard", {
                    method: "get",
                    preserveState: false,
                    preserveScroll: false,
                    onBefore: () => {
                        console.warn(
                            "Access forbidden - redirecting to dashboard"
                        );
                    },
                });
                return;
            }

            // Handle 404 Not Found - redirect to dashboard with error message
            if (status === 404) {
                router.visit("/dashboard", {
                    method: "get",
                    preserveState: false,
                    onBefore: () => {
                        console.warn(
                            "Resource not found - redirecting to dashboard"
                        );
                    },
                });
                return;
            }

            // Handle 419 CSRF Token Mismatch - reload the page
            if (status === 419) {
                console.error("CSRF token mismatch - reloading page");
                window.location.reload();
                return;
            }

            // Handle 429 Too Many Requests - show warning
            if (status === 429) {
                console.warn("Too many requests - please slow down");
                // The flash message will be shown via Toast component
                return;
            }

            // Handle 500+ Server Errors - log and show generic error
            if (status >= 500) {
                console.error(
                    `Server error (${status}) - please try again later`
                );
                // The flash message will be shown via Toast component
                return;
            }
        };

        // Listen for Inertia error events
        document.addEventListener("inertia:error", handleError);

        return () => {
            document.removeEventListener("inertia:error", handleError);
        };
    }, []);
}

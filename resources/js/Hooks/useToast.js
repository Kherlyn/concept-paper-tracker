import { router } from "@inertiajs/react";

/**
 * Custom hook to trigger toast notifications programmatically
 * Uses Inertia's flash messages to show toasts
 */
export default function useToast() {
    const showToast = (message, type = "success") => {
        // Use Inertia's visit with preserveState to flash a message
        router.reload({
            only: [],
            preserveState: true,
            preserveScroll: true,
            onSuccess: (page) => {
                // Flash message will be picked up by Toast component
                page.props.flash = {
                    ...page.props.flash,
                    [type]: message,
                };
            },
        });
    };

    return {
        success: (message) => showToast(message, "success"),
        error: (message) => showToast(message, "error"),
        warning: (message) => showToast(message, "warning"),
        info: (message) => showToast(message, "info"),
    };
}

import { useEffect, useRef } from "react";

/**
 * ValidationErrors component
 * Displays a summary of all validation errors at the top of a form
 * Automatically scrolls to errors and announces them to screen readers
 */
export default function ValidationErrors({ errors, className = "" }) {
    const errorRef = useRef(null);
    const errorMessages = Object.values(errors || {}).flat();

    // Scroll to errors when they appear
    useEffect(() => {
        if (errorMessages.length > 0 && errorRef.current) {
            errorRef.current.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
            // Focus the error container for screen readers
            errorRef.current.focus();
        }
    }, [errorMessages.length]);

    if (errorMessages.length === 0) {
        return null;
    }

    return (
        <div
            ref={errorRef}
            className={`rounded-md bg-red-50 p-4 ${className}`}
            role="alert"
            aria-live="assertive"
            tabIndex="-1"
        >
            <div className="flex">
                <div className="flex-shrink-0">
                    <svg
                        className="h-5 w-5 text-red-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                        aria-hidden="true"
                    >
                        <path
                            fillRule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clipRule="evenodd"
                        />
                    </svg>
                </div>
                <div className="ml-3">
                    <h3 className="text-sm font-medium text-red-800">
                        {errorMessages.length === 1
                            ? "There was 1 error with your submission"
                            : `There were ${errorMessages.length} errors with your submission`}
                    </h3>
                    <div className="mt-2 text-sm text-red-700">
                        <ul className="list-disc space-y-1 pl-5">
                            {errorMessages.map((error, index) => (
                                <li key={index}>{error}</li>
                            ))}
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
}

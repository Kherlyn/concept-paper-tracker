import { useState, useEffect } from "react";
import axios from "axios";

/**
 * DiscrepancySummary Component
 *
 * Displays a summary list of all discrepancies identified on a concept paper.
 * Shows discrepancy location (document, page number), user who created it,
 * timestamp, and comment text. Provides navigation to jump to discrepancy in preview.
 */
export default function DiscrepancySummary({
    conceptPaperId = null,
    onNavigateToDiscrepancy = () => {},
    className = "",
}) {
    const [discrepancies, setDiscrepancies] = useState([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);

    useEffect(() => {
        if (conceptPaperId) {
            loadDiscrepancies();
        }
    }, [conceptPaperId]);

    const loadDiscrepancies = async () => {
        setLoading(true);
        setError(null);

        try {
            const response = await axios.get(
                route("concept-papers.discrepancies", conceptPaperId)
            );

            if (response.data.success) {
                setDiscrepancies(response.data.data);
            }
        } catch (err) {
            console.error("Error loading discrepancies:", err);
            setError(
                err.response?.data?.message ||
                    "Failed to load discrepancies. Please try again."
            );
        } finally {
            setLoading(false);
        }
    };

    const formatDate = (dateString) => {
        if (!dateString) return "N/A";
        return new Date(dateString).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    const handleNavigate = (discrepancy) => {
        onNavigateToDiscrepancy({
            attachmentId: discrepancy.attachment_id,
            pageNumber: discrepancy.page_number,
            annotationId: discrepancy.id,
            fileName: discrepancy.attachment?.file_name || "Document",
        });
    };

    if (loading) {
        return (
            <div className={`bg-white shadow-sm sm:rounded-lg ${className}`}>
                <div className="p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                        Discrepancies
                    </h3>
                    <div className="flex items-center justify-center py-8">
                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                        <span className="ml-3 text-gray-600">
                            Loading discrepancies...
                        </span>
                    </div>
                </div>
            </div>
        );
    }

    if (error) {
        return (
            <div className={`bg-white shadow-sm sm:rounded-lg ${className}`}>
                <div className="p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-4">
                        Discrepancies
                    </h3>
                    <div className="bg-red-50 border border-red-200 rounded-lg p-4">
                        <div className="flex items-start">
                            <svg
                                className="h-5 w-5 text-red-400 flex-shrink-0"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                            <div className="ml-3">
                                <p className="text-sm text-red-700">{error}</p>
                                <button
                                    onClick={loadDiscrepancies}
                                    className="mt-2 text-sm font-medium text-red-600 hover:text-red-500"
                                >
                                    Try again
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className={`bg-white shadow-sm sm:rounded-lg ${className}`}>
            <div className="p-6">
                <div className="flex items-center justify-between mb-4">
                    <h3 className="text-lg font-semibold text-gray-900">
                        Discrepancies
                    </h3>
                    {discrepancies.length > 0 && (
                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            {discrepancies.length}{" "}
                            {discrepancies.length === 1 ? "issue" : "issues"}
                        </span>
                    )}
                </div>

                {discrepancies.length === 0 ? (
                    <div className="text-center py-8">
                        <svg
                            className="mx-auto h-12 w-12 text-gray-400"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                        <p className="mt-2 text-sm text-gray-500">
                            No discrepancies found
                        </p>
                        <p className="text-xs text-gray-400 mt-1">
                            All documents have been reviewed without issues
                        </p>
                    </div>
                ) : (
                    <div className="space-y-4">
                        {discrepancies.map((discrepancy) => (
                            <div
                                key={discrepancy.id}
                                className="border border-red-200 rounded-lg p-4 bg-red-50 hover:bg-red-100 transition-colors"
                            >
                                {/* Header with location and user info */}
                                <div className="flex items-start justify-between mb-3">
                                    <div className="flex items-start space-x-3 min-w-0 flex-1">
                                        <div className="flex-shrink-0">
                                            <div className="h-8 w-8 rounded-full bg-red-200 flex items-center justify-center">
                                                <svg
                                                    className="h-5 w-5 text-red-600"
                                                    fill="none"
                                                    viewBox="0 0 24 24"
                                                    stroke="currentColor"
                                                >
                                                    <path
                                                        strokeLinecap="round"
                                                        strokeLinejoin="round"
                                                        strokeWidth={2}
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                                    />
                                                </svg>
                                            </div>
                                        </div>
                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-center space-x-2 mb-1">
                                                <span className="text-sm font-medium text-gray-900 truncate">
                                                    {discrepancy.attachment
                                                        ?.file_name ||
                                                        "Unknown Document"}
                                                </span>
                                                <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-200 text-red-800">
                                                    Page{" "}
                                                    {discrepancy.page_number}
                                                </span>
                                            </div>
                                            <div className="flex items-center space-x-2 text-xs text-gray-600">
                                                <span>
                                                    Reported by{" "}
                                                    <span className="font-medium">
                                                        {discrepancy.user
                                                            ?.name ||
                                                            "Unknown User"}
                                                    </span>
                                                </span>
                                                <span>•</span>
                                                <span>
                                                    {formatDate(
                                                        discrepancy.created_at
                                                    )}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {/* Comment */}
                                {discrepancy.comment && (
                                    <div className="mb-3 pl-11">
                                        <p className="text-sm text-gray-800 whitespace-pre-wrap">
                                            {discrepancy.comment}
                                        </p>
                                    </div>
                                )}

                                {/* Action button */}
                                <div className="pl-11">
                                    <button
                                        onClick={() =>
                                            handleNavigate(discrepancy)
                                        }
                                        className="inline-flex items-center px-3 py-1.5 border border-red-300 rounded-md text-xs font-medium text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors"
                                    >
                                        <svg
                                            className="h-4 w-4 mr-1.5"
                                            fill="none"
                                            viewBox="0 0 24 24"
                                            stroke="currentColor"
                                        >
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                            />
                                            <path
                                                strokeLinecap="round"
                                                strokeLinejoin="round"
                                                strokeWidth={2}
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                            />
                                        </svg>
                                        View in Document
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>
        </div>
    );
}

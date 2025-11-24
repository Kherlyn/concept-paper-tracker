import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import StatusBadge from "@/Components/StatusBadge";
import WorkflowTimeline from "@/Components/WorkflowTimeline";
import StageActionModal from "@/Components/StageActionModal";
import DocumentPreviewWithAnnotations from "@/Components/DocumentPreviewWithAnnotations";
import DiscrepancySummary from "@/Components/DiscrepancySummary";
import { Head, Link, router, usePage } from "@inertiajs/react";
import { useState, useEffect } from "react";

export default function Show({ paper, status_summary }) {
    const { auth, flash } = usePage().props;
    const [showCompleteModal, setShowCompleteModal] = useState(false);
    const [showReturnModal, setShowReturnModal] = useState(false);
    const [showRejectModal, setShowRejectModal] = useState(false);
    const [showAttachmentModal, setShowAttachmentModal] = useState(false);
    const [showSuccessMessage, setShowSuccessMessage] = useState(false);
    const [showErrorMessage, setShowErrorMessage] = useState(false);
    const [showPreviewModal, setShowPreviewModal] = useState(false);
    const [previewAttachment, setPreviewAttachment] = useState(null);

    // Handle flash messages
    useEffect(() => {
        if (flash?.success) {
            setShowSuccessMessage(true);
            setTimeout(() => setShowSuccessMessage(false), 5000);
        }
        if (flash?.error) {
            setShowErrorMessage(true);
            setTimeout(() => setShowErrorMessage(false), 5000);
        }
    }, [flash]);

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

    const formatFileSize = (bytes) => {
        if (bytes === 0) return "0 Bytes";
        const k = 1024;
        const sizes = ["Bytes", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return (
            Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i]
        );
    };

    const handleDownloadAttachment = (attachmentId) => {
        window.location.href = route("attachments.download", attachmentId);
    };

    const handlePreviewAttachment = (attachment) => {
        setPreviewAttachment(attachment);
        setShowPreviewModal(true);
    };

    const handleClosePreview = () => {
        setShowPreviewModal(false);
        setPreviewAttachment(null);
    };

    const handleNavigateToDiscrepancy = (discrepancyInfo) => {
        // Find the attachment by ID
        const attachment = paper.attachments.find(
            (att) => att.id === discrepancyInfo.attachmentId
        );

        if (attachment) {
            setPreviewAttachment(attachment);
            setShowPreviewModal(true);
            // Note: The page navigation would need to be handled by the DocumentPreview component
            // We could pass the page number as a prop if needed
        }
    };

    const currentStage = paper.stages.find(
        (stage) => stage.id === paper.current_stage?.id
    );

    const canCompleteStage =
        currentStage &&
        currentStage.assigned_role === auth.user.role &&
        currentStage.status === "in_progress";

    const canReturnStage =
        currentStage &&
        currentStage.assigned_role === auth.user.role &&
        currentStage.status === "in_progress" &&
        currentStage.stage_order > 1;

    const canAddAttachment =
        currentStage &&
        currentStage.assigned_role === auth.user.role &&
        currentStage.status === "in_progress";

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <div className="min-w-0">
                        <h2 className="text-lg sm:text-xl font-semibold leading-tight text-gray-800">
                            Concept Paper Details
                        </h2>
                        <p className="text-xs sm:text-sm text-gray-600 mt-1 truncate">
                            {paper.tracking_number}
                        </p>
                    </div>
                    <Link
                        href={route("concept-papers.index")}
                        className="text-indigo-600 hover:text-indigo-900 text-sm font-medium whitespace-nowrap"
                    >
                        ← Back to List
                    </Link>
                </div>
            }
        >
            <Head title={`Concept Paper - ${paper.tracking_number}`} />

            <div className="py-6 sm:py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                    {/* Success Message */}
                    {showSuccessMessage && flash?.success && (
                        <div className="rounded-md bg-green-50 p-4 border border-green-200">
                            <div className="flex">
                                <div className="flex-shrink-0">
                                    <svg
                                        className="h-5 w-5 text-green-400"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fillRule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div className="ml-3">
                                    <p className="text-sm font-medium text-green-800">
                                        {flash.success}
                                    </p>
                                </div>
                                <div className="ml-auto pl-3">
                                    <div className="-mx-1.5 -my-1.5">
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setShowSuccessMessage(false)
                                            }
                                            className="inline-flex rounded-md bg-green-50 p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 focus:ring-offset-green-50"
                                        >
                                            <span className="sr-only">
                                                Dismiss
                                            </span>
                                            <svg
                                                className="h-5 w-5"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fillRule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clipRule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Error Message */}
                    {showErrorMessage && flash?.error && (
                        <div className="rounded-md bg-red-50 p-4 border border-red-200">
                            <div className="flex">
                                <div className="flex-shrink-0">
                                    <svg
                                        className="h-5 w-5 text-red-400"
                                        viewBox="0 0 20 20"
                                        fill="currentColor"
                                    >
                                        <path
                                            fillRule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </div>
                                <div className="ml-3">
                                    <p className="text-sm font-medium text-red-800">
                                        {flash.error}
                                    </p>
                                </div>
                                <div className="ml-auto pl-3">
                                    <div className="-mx-1.5 -my-1.5">
                                        <button
                                            type="button"
                                            onClick={() =>
                                                setShowErrorMessage(false)
                                            }
                                            className="inline-flex rounded-md bg-red-50 p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-red-600 focus:ring-offset-2 focus:ring-offset-red-50"
                                        >
                                            <span className="sr-only">
                                                Dismiss
                                            </span>
                                            <svg
                                                className="h-5 w-5"
                                                viewBox="0 0 20 20"
                                                fill="currentColor"
                                            >
                                                <path
                                                    fillRule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clipRule="evenodd"
                                                />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}
                    {/* Paper Information Card */}
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-4 sm:p-6">
                            <div className="flex flex-col sm:flex-row sm:items-start sm:justify-between mb-6 gap-3">
                                <div className="min-w-0 flex-1">
                                    <h3 className="text-base sm:text-lg font-semibold text-gray-900 break-words">
                                        {paper.title}
                                    </h3>
                                    <div className="mt-2 flex flex-wrap items-center gap-2">
                                        <StatusBadge status={paper.status} />
                                        {paper.nature_of_request && (
                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                                {paper.nature_of_request
                                                    .charAt(0)
                                                    .toUpperCase() +
                                                    paper.nature_of_request.slice(
                                                        1
                                                    )}
                                            </span>
                                        )}
                                        {/* Student Involvement Badge */}
                                        {paper.students_involved !==
                                            undefined && (
                                            <span
                                                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                                    paper.students_involved
                                                        ? "bg-blue-100 text-blue-800 border border-blue-200"
                                                        : "bg-gray-100 text-gray-800 border border-gray-200"
                                                }`}
                                            >
                                                {paper.students_involved ? (
                                                    <>
                                                        <svg
                                                            className="mr-1 h-3 w-3"
                                                            fill="currentColor"
                                                            viewBox="0 0 20 20"
                                                        >
                                                            <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z" />
                                                        </svg>
                                                        Students Involved
                                                    </>
                                                ) : (
                                                    "No Students"
                                                )}
                                            </span>
                                        )}
                                    </div>
                                </div>
                            </div>

                            <dl className="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">
                                        Requisitioner
                                    </dt>
                                    <dd className="mt-1 text-sm text-gray-900">
                                        {paper.requisitioner.name}
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">
                                        Department
                                    </dt>
                                    <dd className="mt-1 text-sm text-gray-900">
                                        {paper.department}
                                    </dd>
                                </div>
                                <div>
                                    <dt className="text-sm font-medium text-gray-500">
                                        Submitted
                                    </dt>
                                    <dd className="mt-1 text-sm text-gray-900">
                                        {formatDate(paper.submitted_at)}
                                    </dd>
                                </div>
                                {/* Deadline Date - Display prominently */}
                                {paper.deadline_date && (
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">
                                            Deadline
                                        </dt>
                                        <dd
                                            className={`mt-1 text-sm font-semibold ${
                                                paper.is_deadline_reached
                                                    ? "text-red-600"
                                                    : "text-gray-900"
                                            }`}
                                        >
                                            {formatDate(paper.deadline_date)}
                                            {paper.is_deadline_reached && (
                                                <span className="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                    ⚠ Deadline Reached
                                                </span>
                                            )}
                                        </dd>
                                    </div>
                                )}
                                {paper.completed_at && (
                                    <div>
                                        <dt className="text-sm font-medium text-gray-500">
                                            Completed
                                        </dt>
                                        <dd className="mt-1 text-sm text-gray-900">
                                            {formatDate(paper.completed_at)}
                                        </dd>
                                    </div>
                                )}
                            </dl>
                        </div>
                    </div>

                    {/* Workflow Timeline */}
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-4 sm:p-6">
                            <h3 className="text-base sm:text-lg font-semibold text-gray-900 mb-4">
                                Workflow Progress
                            </h3>
                            <WorkflowTimeline
                                stages={paper.stages}
                                currentStage={currentStage}
                            />
                        </div>
                    </div>

                    {/* Stage Cards */}
                    <div className="grid grid-cols-1 gap-4 sm:gap-6 lg:grid-cols-2">
                        {paper.stages.map((stage) => (
                            <div
                                key={stage.id}
                                className={`overflow-hidden bg-white shadow-sm sm:rounded-lg border-2 ${
                                    stage.id === currentStage?.id
                                        ? "border-blue-500"
                                        : "border-transparent"
                                }`}
                            >
                                <div className="p-4 sm:p-6">
                                    <div className="flex items-start justify-between mb-4">
                                        <div>
                                            <h4 className="text-base font-semibold text-gray-900">
                                                {stage.stage_name}
                                            </h4>
                                            <p className="text-xs text-gray-500 mt-1">
                                                Stage {stage.stage_order} of 9
                                            </p>
                                        </div>
                                        <StatusBadge status={stage.status} />
                                    </div>

                                    <dl className="space-y-3">
                                        {stage.assigned_user && (
                                            <div>
                                                <dt className="text-xs font-medium text-gray-500">
                                                    Assigned To
                                                </dt>
                                                <dd className="mt-1 text-sm text-gray-900">
                                                    {stage.assigned_user.name}
                                                </dd>
                                            </div>
                                        )}
                                        {stage.deadline && (
                                            <div>
                                                <dt className="text-xs font-medium text-gray-500">
                                                    Deadline
                                                </dt>
                                                <dd
                                                    className={`mt-1 text-sm ${
                                                        stage.is_overdue
                                                            ? "text-red-600 font-semibold"
                                                            : "text-gray-900"
                                                    }`}
                                                >
                                                    {formatDate(stage.deadline)}
                                                    {stage.is_overdue && (
                                                        <span className="ml-2">
                                                            ⚠ Overdue
                                                        </span>
                                                    )}
                                                </dd>
                                            </div>
                                        )}
                                        {stage.completed_at && (
                                            <div>
                                                <dt className="text-xs font-medium text-gray-500">
                                                    Completed At
                                                </dt>
                                                <dd className="mt-1 text-sm text-gray-900">
                                                    {formatDate(
                                                        stage.completed_at
                                                    )}
                                                </dd>
                                            </div>
                                        )}
                                        {stage.remarks && (
                                            <div>
                                                <dt className="text-xs font-medium text-gray-500">
                                                    Remarks
                                                </dt>
                                                <dd className="mt-1 text-sm text-gray-700 italic">
                                                    {stage.remarks}
                                                </dd>
                                            </div>
                                        )}
                                        {stage.attachments.length > 0 && (
                                            <div>
                                                <dt className="text-xs font-medium text-gray-500 mb-2">
                                                    Attachments
                                                </dt>
                                                <dd className="space-y-1">
                                                    {stage.attachments.map(
                                                        (attachment) => (
                                                            <div
                                                                key={
                                                                    attachment.id
                                                                }
                                                                className="flex items-center justify-between text-xs bg-gray-50 p-2 rounded"
                                                            >
                                                                <span className="text-gray-700 truncate">
                                                                    {
                                                                        attachment.file_name
                                                                    }
                                                                </span>
                                                                <div className="flex items-center space-x-2 ml-2">
                                                                    <button
                                                                        onClick={() =>
                                                                            handlePreviewAttachment(
                                                                                attachment
                                                                            )
                                                                        }
                                                                        className="text-indigo-600 hover:text-indigo-900"
                                                                    >
                                                                        Preview
                                                                    </button>
                                                                    <button
                                                                        onClick={() =>
                                                                            handleDownloadAttachment(
                                                                                attachment.id
                                                                            )
                                                                        }
                                                                        className="text-indigo-600 hover:text-indigo-900"
                                                                    >
                                                                        Download
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        )
                                                    )}
                                                </dd>
                                            </div>
                                        )}
                                    </dl>

                                    {/* Action Buttons for Current Stage */}
                                    {stage.id === currentStage?.id && (
                                        <div className="mt-4 pt-4 border-t border-gray-200 flex flex-col sm:flex-row flex-wrap gap-2">
                                            {canCompleteStage && (
                                                <button
                                                    onClick={() =>
                                                        setShowCompleteModal(
                                                            true
                                                        )
                                                    }
                                                    className="inline-flex items-center justify-center px-3 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                >
                                                    <svg
                                                        className="h-4 w-4 mr-1"
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
                                                    Approve with Signature
                                                </button>
                                            )}
                                            {canCompleteStage && (
                                                <button
                                                    onClick={() =>
                                                        setShowRejectModal(true)
                                                    }
                                                    className="inline-flex items-center justify-center px-3 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                >
                                                    <svg
                                                        className="h-4 w-4 mr-1"
                                                        fill="none"
                                                        viewBox="0 0 24 24"
                                                        stroke="currentColor"
                                                    >
                                                        <path
                                                            strokeLinecap="round"
                                                            strokeLinejoin="round"
                                                            strokeWidth={2}
                                                            d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        />
                                                    </svg>
                                                    Reject Paper
                                                </button>
                                            )}
                                            {canReturnStage && (
                                                <button
                                                    onClick={() =>
                                                        setShowReturnModal(true)
                                                    }
                                                    className="inline-flex items-center justify-center px-3 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                >
                                                    Return to Previous
                                                </button>
                                            )}
                                            {canAddAttachment && (
                                                <button
                                                    onClick={() =>
                                                        setShowAttachmentModal(
                                                            true
                                                        )
                                                    }
                                                    className="inline-flex items-center justify-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                >
                                                    Add Attachment
                                                </button>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Attachments Section */}
                    {paper.attachments.length > 0 && (
                        <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                            <div className="p-6">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                    Concept Paper Attachments
                                </h3>
                                <div className="space-y-3">
                                    {paper.attachments.map((attachment) => (
                                        <div
                                            key={attachment.id}
                                            className="flex items-center justify-between p-4 bg-gray-50 rounded-lg"
                                        >
                                            <div className="flex items-center space-x-3">
                                                <svg
                                                    className="h-8 w-8 text-red-500"
                                                    fill="currentColor"
                                                    viewBox="0 0 20 20"
                                                >
                                                    <path
                                                        fillRule="evenodd"
                                                        d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                        clipRule="evenodd"
                                                    />
                                                </svg>
                                                <div>
                                                    <p className="text-sm font-medium text-gray-900">
                                                        {attachment.file_name}
                                                    </p>
                                                    <p className="text-xs text-gray-500">
                                                        {formatFileSize(
                                                            attachment.file_size
                                                        )}{" "}
                                                        • Uploaded by{" "}
                                                        {
                                                            attachment.uploader
                                                                .name
                                                        }{" "}
                                                        on{" "}
                                                        {formatDate(
                                                            attachment.uploaded_at
                                                        )}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="flex items-center space-x-2">
                                                <button
                                                    onClick={() =>
                                                        handlePreviewAttachment(
                                                            attachment
                                                        )
                                                    }
                                                    className="inline-flex items-center px-3 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                >
                                                    Preview
                                                </button>
                                                <button
                                                    onClick={() =>
                                                        handleDownloadAttachment(
                                                            attachment.id
                                                        )
                                                    }
                                                    className="inline-flex items-center px-3 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                >
                                                    Download
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Discrepancy Summary */}
                    <DiscrepancySummary
                        conceptPaperId={paper.id}
                        onNavigateToDiscrepancy={handleNavigateToDiscrepancy}
                    />

                    {/* Audit Trail */}
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                Audit Trail
                            </h3>
                            {paper.audit_logs.length > 0 ? (
                                <div className="flow-root">
                                    <ul className="-mb-8">
                                        {paper.audit_logs.map((log, index) => (
                                            <li key={log.id}>
                                                <div className="relative pb-8">
                                                    {index !==
                                                        paper.audit_logs
                                                            .length -
                                                            1 && (
                                                        <span
                                                            className="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"
                                                            aria-hidden="true"
                                                        />
                                                    )}
                                                    <div className="relative flex space-x-3">
                                                        <div>
                                                            <span className="h-8 w-8 rounded-full bg-gray-100 flex items-center justify-center ring-8 ring-white">
                                                                <svg
                                                                    className="h-5 w-5 text-gray-500"
                                                                    fill="currentColor"
                                                                    viewBox="0 0 20 20"
                                                                >
                                                                    <path
                                                                        fillRule="evenodd"
                                                                        d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"
                                                                        clipRule="evenodd"
                                                                    />
                                                                </svg>
                                                            </span>
                                                        </div>
                                                        <div className="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                                            <div>
                                                                <p className="text-sm text-gray-900">
                                                                    <span className="font-medium">
                                                                        {
                                                                            log
                                                                                .user
                                                                                .name
                                                                        }
                                                                    </span>{" "}
                                                                    {log.action}
                                                                    {log.stage_name && (
                                                                        <span className="text-gray-600">
                                                                            {" "}
                                                                            -{" "}
                                                                            {
                                                                                log.stage_name
                                                                            }
                                                                        </span>
                                                                    )}
                                                                </p>
                                                                {log.remarks && (
                                                                    <p className="mt-1 text-sm text-gray-600 italic">
                                                                        "
                                                                        {
                                                                            log.remarks
                                                                        }
                                                                        "
                                                                    </p>
                                                                )}
                                                            </div>
                                                            <div className="whitespace-nowrap text-right text-sm text-gray-500">
                                                                <time
                                                                    dateTime={
                                                                        log.created_at
                                                                    }
                                                                >
                                                                    {formatDate(
                                                                        log.created_at
                                                                    )}
                                                                </time>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        ))}
                                    </ul>
                                </div>
                            ) : (
                                <p className="text-sm text-gray-500">
                                    No audit trail entries yet.
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Stage Action Modals */}
            {currentStage && (
                <>
                    <StageActionModal
                        isOpen={showCompleteModal}
                        onClose={() => setShowCompleteModal(false)}
                        stage={currentStage}
                        action="complete"
                        conceptPaperId={paper.id}
                    />

                    <StageActionModal
                        isOpen={showReturnModal}
                        onClose={() => setShowReturnModal(false)}
                        stage={currentStage}
                        action="return"
                        conceptPaperId={paper.id}
                    />

                    <StageActionModal
                        isOpen={showRejectModal}
                        onClose={() => setShowRejectModal(false)}
                        stage={currentStage}
                        action="reject"
                        conceptPaperId={paper.id}
                    />

                    <StageActionModal
                        isOpen={showAttachmentModal}
                        onClose={() => setShowAttachmentModal(false)}
                        stage={currentStage}
                        action="attachment"
                        conceptPaperId={paper.id}
                    />
                </>
            )}

            {/* Document Preview Modal with Annotations */}
            {previewAttachment && (
                <DocumentPreviewWithAnnotations
                    show={showPreviewModal}
                    onClose={handleClosePreview}
                    attachmentId={previewAttachment.id}
                    conceptPaperId={paper.id}
                    fileName={previewAttachment.file_name}
                    readOnly={false}
                />
            )}
        </AuthenticatedLayout>
    );
}

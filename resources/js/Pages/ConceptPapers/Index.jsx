import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import StatusBadge from "@/Components/StatusBadge";
import DataTable from "@/Components/DataTable";
import { Head, Link, router, usePage } from "@inertiajs/react";
import { useState } from "react";

export default function Index({ papers, filters }) {
    const { auth } = usePage().props;
    const [statusFilter, setStatusFilter] = useState(filters.status || "");

    const handleStatusFilterChange = (status) => {
        setStatusFilter(status);
        router.get(
            route("concept-papers.index"),
            { status: status || undefined },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    const formatDate = (dateString) => {
        if (!dateString) return "N/A";
        return new Date(dateString).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    };

    const columns = [
        {
            key: "tracking_number",
            label: "Tracking #",
            sortable: true,
            render: (paper) => (
                <Link
                    href={route("concept-papers.show", paper.id)}
                    className="text-indigo-600 hover:text-indigo-900 font-medium"
                >
                    {paper.tracking_number}
                </Link>
            ),
        },
        {
            key: "title",
            label: "Title",
            sortable: true,
            render: (paper) => (
                <div className="max-w-xs truncate" title={paper.title}>
                    {paper.title}
                </div>
            ),
        },
        {
            key: "department",
            label: "Department",
            sortable: true,
        },
        {
            key: "current_stage",
            label: "Current Stage",
            sortable: false,
            render: (paper) => (
                <div>
                    {paper.current_stage ? (
                        <div>
                            <div className="text-sm font-medium text-gray-900">
                                {paper.current_stage.stage_name}
                            </div>
                            {paper.current_stage.is_overdue && (
                                <div className="text-xs text-red-600 mt-0.5 font-semibold">
                                    ⚠ Overdue
                                </div>
                            )}
                        </div>
                    ) : (
                        <span className="text-gray-500 text-sm">N/A</span>
                    )}
                </div>
            ),
        },
        {
            key: "status",
            label: "Status",
            sortable: true,
            accessor: (paper) => paper.status,
            render: (paper) => <StatusBadge status={paper.status} />,
        },
        {
            key: "submitted_at",
            label: "Submitted",
            sortable: true,
            accessor: (paper) => paper.submitted_at,
            render: (paper) => formatDate(paper.submitted_at),
        },
        {
            key: "actions",
            label: "Actions",
            sortable: false,
            render: (paper) => (
                <div className="flex items-center space-x-2">
                    <Link
                        href={route("concept-papers.show", paper.id)}
                        className="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                    >
                        View
                    </Link>
                    {/* Quick action for approvers to complete stage */}
                    {paper.current_stage &&
                        paper.current_stage.assigned_user_id === auth.user.id &&
                        paper.current_stage.status === "in_progress" && (
                            <Link
                                href={route("concept-papers.show", paper.id)}
                                className="text-green-600 hover:text-green-900 text-sm font-medium"
                            >
                                Action Required
                            </Link>
                        )}
                    {/* Admin delete action */}
                    {auth.user.role === "admin" && (
                        <button
                            onClick={() => handleDelete(paper.id)}
                            className="text-red-600 hover:text-red-900 text-sm font-medium"
                        >
                            Delete
                        </button>
                    )}
                </div>
            ),
        },
    ];

    const handleDelete = (paperId) => {
        if (
            confirm(
                "Are you sure you want to delete this concept paper? This action cannot be undone."
            )
        ) {
            router.delete(route("concept-papers.destroy", paperId), {
                preserveScroll: true,
            });
        }
    };

    const getPageTitle = () => {
        switch (auth.user.role) {
            case "requisitioner":
                return "My Concept Papers";
            case "admin":
                return "All Concept Papers";
            default:
                return "Pending Approvals";
        }
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        {getPageTitle()}
                    </h2>
                    {auth.user.role === "requisitioner" && (
                        <Link
                            href={route("concept-papers.create")}
                            className="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                        >
                            Submit New Paper
                        </Link>
                    )}
                </div>
            }
        >
            <Head title={getPageTitle()} />

            <div className="py-6 sm:py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">
                    {/* Summary Stats */}
                    <div className="grid grid-cols-1 gap-4 sm:gap-5 sm:grid-cols-2 lg:grid-cols-4">
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-5">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="rounded-md bg-indigo-500 p-3">
                                            <svg
                                                className="h-6 w-6 text-white"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Total Papers
                                            </dt>
                                            <dd className="text-2xl font-semibold text-gray-900">
                                                {papers.length}
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-5">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="rounded-md bg-blue-500 p-3">
                                            <svg
                                                className="h-6 w-6 text-white"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                In Progress
                                            </dt>
                                            <dd className="text-2xl font-semibold text-gray-900">
                                                {
                                                    papers.filter(
                                                        (p) =>
                                                            p.status ===
                                                            "in_progress"
                                                    ).length
                                                }
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-5">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="rounded-md bg-green-500 p-3">
                                            <svg
                                                className="h-6 w-6 text-white"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Completed
                                            </dt>
                                            <dd className="text-2xl font-semibold text-gray-900">
                                                {
                                                    papers.filter(
                                                        (p) =>
                                                            p.status ===
                                                            "completed"
                                                    ).length
                                                }
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-5">
                                <div className="flex items-center">
                                    <div className="flex-shrink-0">
                                        <div className="rounded-md bg-red-500 p-3">
                                            <svg
                                                className="h-6 w-6 text-white"
                                                fill="none"
                                                stroke="currentColor"
                                                viewBox="0 0 24 24"
                                            >
                                                <path
                                                    strokeLinecap="round"
                                                    strokeLinejoin="round"
                                                    strokeWidth="2"
                                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                />
                                            </svg>
                                        </div>
                                    </div>
                                    <div className="ml-5 w-0 flex-1">
                                        <dl>
                                            <dt className="text-sm font-medium text-gray-500 truncate">
                                                Overdue
                                            </dt>
                                            <dd className="text-2xl font-semibold text-gray-900">
                                                {
                                                    papers.filter(
                                                        (p) => p.is_overdue
                                                    ).length
                                                }
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Main Table Card */}
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-4 sm:p-6">
                            {/* Status Filter */}
                            <div className="mb-6">
                                <label
                                    htmlFor="status-filter"
                                    className="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Filter by Status
                                </label>
                                <div
                                    className="flex flex-wrap gap-2"
                                    role="group"
                                    aria-label="Status filter buttons"
                                    id="status-filter"
                                >
                                    <button
                                        onClick={() =>
                                            handleStatusFilterChange("")
                                        }
                                        className={`px-4 py-2 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ${
                                            statusFilter === ""
                                                ? "bg-indigo-600 text-white"
                                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                                        }`}
                                        aria-pressed={statusFilter === ""}
                                    >
                                        All
                                    </button>
                                    <button
                                        onClick={() =>
                                            handleStatusFilterChange("pending")
                                        }
                                        className={`px-4 py-2 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ${
                                            statusFilter === "pending"
                                                ? "bg-indigo-600 text-white"
                                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                                        }`}
                                        aria-pressed={
                                            statusFilter === "pending"
                                        }
                                    >
                                        Pending
                                    </button>
                                    <button
                                        onClick={() =>
                                            handleStatusFilterChange(
                                                "in_progress"
                                            )
                                        }
                                        className={`px-4 py-2 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ${
                                            statusFilter === "in_progress"
                                                ? "bg-indigo-600 text-white"
                                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                                        }`}
                                        aria-pressed={
                                            statusFilter === "in_progress"
                                        }
                                    >
                                        In Progress
                                    </button>
                                    <button
                                        onClick={() =>
                                            handleStatusFilterChange(
                                                "completed"
                                            )
                                        }
                                        className={`px-4 py-2 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ${
                                            statusFilter === "completed"
                                                ? "bg-indigo-600 text-white"
                                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                                        }`}
                                        aria-pressed={
                                            statusFilter === "completed"
                                        }
                                    >
                                        Completed
                                    </button>
                                    <button
                                        onClick={() =>
                                            handleStatusFilterChange("returned")
                                        }
                                        className={`px-4 py-2 rounded-md text-sm font-medium transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 ${
                                            statusFilter === "returned"
                                                ? "bg-indigo-600 text-white"
                                                : "bg-gray-100 text-gray-700 hover:bg-gray-200"
                                        }`}
                                        aria-pressed={
                                            statusFilter === "returned"
                                        }
                                    >
                                        Returned
                                    </button>
                                </div>
                            </div>

                            {/* Data Table */}
                            <DataTable
                                columns={columns}
                                data={papers}
                                sortable={true}
                                filterable={true}
                                pagination={true}
                                itemsPerPage={15}
                                emptyMessage="No concept papers found"
                            />
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

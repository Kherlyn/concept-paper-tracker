import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import StatusBadge from "@/Components/StatusBadge";

export default function Dashboard({
    user,
    dashboard_data,
    pending_tasks,
    overdue_items,
    recent_activity,
    statistics,
}) {
    const formatDate = (dateString) => {
        if (!dateString) return "N/A";
        return new Date(dateString).toLocaleDateString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
        });
    };

    const formatDateTime = (dateString) => {
        if (!dateString) return "N/A";
        return new Date(dateString).toLocaleString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    const getRoleDisplayName = (role) => {
        const roleNames = {
            requisitioner: "Requisitioner",
            sps: "SPS",
            vp_acad: "VP Academic",
            senior_vp: "Senior VP",
            auditor: "Auditor",
            accounting: "Accounting",
            admin: "Administrator",
        };
        return roleNames[role] || role;
    };

    // Stats cards component
    const StatsCard = ({ title, value, subtitle, color = "blue", icon }) => (
        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div className="p-4 sm:p-6">
                <div className="flex items-center">
                    <div className="flex-shrink-0">
                        <div
                            className={`p-2 sm:p-3 rounded-md bg-${color}-100`}
                        >
                            {icon}
                        </div>
                    </div>
                    <div className="ml-3 sm:ml-5 w-0 flex-1">
                        <dl>
                            <dt className="text-xs sm:text-sm font-medium text-gray-500 truncate">
                                {title}
                            </dt>
                            <dd className="flex items-baseline">
                                <div className="text-xl sm:text-2xl font-semibold text-gray-900">
                                    {value}
                                </div>
                            </dd>
                            {subtitle && (
                                <dd className="text-xs text-gray-500 mt-1">
                                    {subtitle}
                                </dd>
                            )}
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    );

    // Render requisitioner dashboard
    const renderRequisitionerDashboard = () => (
        <>
            {/* Quick Actions for Requisitioner */}
            <div className="mb-8">
                <Link
                    href="/concept-papers/create"
                    className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg
                        className="h-5 w-5 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    Submit New Paper
                </Link>
            </div>

            {/* Stats Cards */}
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
                <StatsCard
                    title="Total Submitted"
                    value={dashboard_data.counts.total}
                    color="blue"
                    icon={
                        <svg
                            className="h-6 w-6 text-blue-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    }
                />
                <StatsCard
                    title="In Progress"
                    value={dashboard_data.counts.in_progress}
                    color="yellow"
                    icon={
                        <svg
                            className="h-6 w-6 text-yellow-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    }
                />
                <StatsCard
                    title="Completed"
                    value={dashboard_data.counts.completed}
                    color="green"
                    icon={
                        <svg
                            className="h-6 w-6 text-green-600"
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
                    }
                />
                <StatsCard
                    title="Returned"
                    value={dashboard_data.counts.returned}
                    color="orange"
                    icon={
                        <svg
                            className="h-6 w-6 text-orange-600"
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
                    }
                />
            </div>

            {/* My Papers */}
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div className="p-4 sm:p-6 border-b border-gray-200">
                    <h3 className="text-lg font-medium text-gray-900">
                        My Concept Papers
                    </h3>
                </div>
                {/* Desktop Table View */}
                <div className="hidden md:block overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tracking Number
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Stage
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Submitted
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {dashboard_data.my_papers.length === 0 ? (
                                <tr>
                                    <td
                                        colSpan="6"
                                        className="px-6 py-4 text-center text-sm text-gray-500"
                                    >
                                        No concept papers submitted yet.
                                    </td>
                                </tr>
                            ) : (
                                dashboard_data.my_papers.map((paper) => (
                                    <tr
                                        key={paper.id}
                                        className={
                                            paper.is_overdue ? "bg-red-50" : ""
                                        }
                                    >
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {paper.tracking_number}
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-900">
                                            {paper.title}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {paper.current_stage
                                                ? paper.current_stage.stage_name
                                                : "N/A"}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <StatusBadge
                                                status={
                                                    paper.is_overdue
                                                        ? "overdue"
                                                        : paper.status
                                                }
                                            />
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {formatDate(paper.submitted_at)}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link
                                                href={`/concept-papers/${paper.id}`}
                                                className="text-blue-600 hover:text-blue-900"
                                            >
                                                View
                                            </Link>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
                {/* Mobile Card View */}
                <div className="md:hidden divide-y divide-gray-200">
                    {dashboard_data.my_papers.length === 0 ? (
                        <div className="p-6 text-center text-sm text-gray-500">
                            No concept papers submitted yet.
                        </div>
                    ) : (
                        dashboard_data.my_papers.map((paper) => (
                            <div
                                key={paper.id}
                                className={`p-4 space-y-3 ${paper.is_overdue ? "bg-red-50" : ""
                                    }`}
                            >
                                <div className="flex items-start justify-between">
                                    <div className="flex-1 min-w-0">
                                        <p className="text-xs font-medium text-gray-500 uppercase">
                                            Tracking Number
                                        </p>
                                        <p className="mt-1 text-sm font-medium text-gray-900">
                                            {paper.tracking_number}
                                        </p>
                                    </div>
                                    <StatusBadge
                                        status={
                                            paper.is_overdue
                                                ? "overdue"
                                                : paper.status
                                        }
                                    />
                                </div>
                                <div>
                                    <p className="text-xs font-medium text-gray-500 uppercase">
                                        Title
                                    </p>
                                    <p className="mt-1 text-sm text-gray-900">
                                        {paper.title}
                                    </p>
                                </div>
                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <p className="text-xs font-medium text-gray-500 uppercase">
                                            Current Stage
                                        </p>
                                        <p className="mt-1 text-sm text-gray-900">
                                            {paper.current_stage
                                                ? paper.current_stage.stage_name
                                                : "N/A"}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-xs font-medium text-gray-500 uppercase">
                                            Submitted
                                        </p>
                                        <p className="mt-1 text-sm text-gray-900">
                                            {formatDate(paper.submitted_at)}
                                        </p>
                                    </div>
                                </div>
                                <div className="pt-2">
                                    <Link
                                        href={`/concept-papers/${paper.id}`}
                                        className="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        View Details
                                    </Link>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </>
    );

    // Render approver dashboard
    const renderApproverDashboard = () => (
        <>
            {/* Quick Actions for Approver */}
            {dashboard_data.counts.overdue > 0 && (
                <div className="mb-8 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div className="flex items-start sm:items-center">
                            <svg
                                className="h-6 w-6 text-red-600 mr-3 flex-shrink-0"
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
                            <div>
                                <h3 className="text-sm font-medium text-red-800">
                                    {dashboard_data.counts.overdue} Overdue
                                    Approval
                                    {dashboard_data.counts.overdue !== 1
                                        ? "s"
                                        : ""}
                                </h3>
                                <p className="text-xs text-red-600 mt-0.5">
                                    These items require immediate attention
                                </p>
                            </div>
                        </div>
                        <Link
                            href="/concept-papers?filter=overdue"
                            className="inline-flex items-center justify-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            View All
                        </Link>
                    </div>
                </div>
            )}

            {/* Stats Cards */}
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-3 mb-8">
                <StatsCard
                    title="Pending Approvals"
                    value={dashboard_data.counts.total_assigned}
                    color="yellow"
                    icon={
                        <svg
                            className="h-6 w-6 text-yellow-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    }
                />
                <StatsCard
                    title="Completed"
                    value={dashboard_data.counts.completed}
                    color="green"
                    icon={
                        <svg
                            className="h-6 w-6 text-green-600"
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
                    }
                />
                <StatsCard
                    title="Overdue"
                    value={dashboard_data.counts.overdue}
                    color="red"
                    icon={
                        <svg
                            className="h-6 w-6 text-red-600"
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
                    }
                />
            </div>

            {/* Action Required Section */}
            {dashboard_data.counts.total_assigned > 0 && (
                <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                    <div className="p-4 sm:p-6 border-b border-gray-200">
                        <h3 className="text-lg font-medium text-gray-900">
                            Action Required
                        </h3>
                        <p className="mt-1 text-sm text-gray-500">
                            Papers awaiting your approval
                        </p>
                    </div>
                    {/* Desktop Table View */}
                    <div className="hidden md:block overflow-x-auto">
                        <table className="min-w-full divide-y divide-gray-200">
                            <thead className="bg-gray-50">
                                <tr>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Tracking Number
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Title
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stage
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Deadline
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody className="bg-white divide-y divide-gray-200">
                                {dashboard_data.assigned_stages.map((stage) => (
                                    <tr
                                        key={stage.id}
                                        className={
                                            stage.is_overdue ? "bg-red-50" : ""
                                        }
                                    >
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {
                                                stage.concept_paper
                                                    .tracking_number
                                            }
                                        </td>
                                        <td className="px-6 py-4 text-sm text-gray-900">
                                            {stage.concept_paper.title}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {stage.stage_name}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {formatDate(stage.deadline)}
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap">
                                            <StatusBadge
                                                status={
                                                    stage.is_overdue
                                                        ? "overdue"
                                                        : stage.status
                                                }
                                            />
                                        </td>
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <Link
                                                href={`/concept-papers/${stage.concept_paper.id}`}
                                                className="text-blue-600 hover:text-blue-900"
                                            >
                                                Review
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                    {/* Mobile Card View */}
                    <div className="md:hidden divide-y divide-gray-200">
                        {dashboard_data.assigned_stages.map((stage) => (
                            <div
                                key={stage.id}
                                className={`p-4 space-y-3 ${stage.is_overdue ? "bg-red-50" : ""
                                    }`}
                            >
                                <div className="flex items-start justify-between">
                                    <div className="flex-1 min-w-0">
                                        <p className="text-xs font-medium text-gray-500 uppercase">
                                            Tracking Number
                                        </p>
                                        <p className="mt-1 text-sm font-medium text-gray-900">
                                            {
                                                stage.concept_paper
                                                    .tracking_number
                                            }
                                        </p>
                                    </div>
                                    <StatusBadge
                                        status={
                                            stage.is_overdue
                                                ? "overdue"
                                                : stage.status
                                        }
                                    />
                                </div>
                                <div>
                                    <p className="text-xs font-medium text-gray-500 uppercase">
                                        Title
                                    </p>
                                    <p className="mt-1 text-sm text-gray-900">
                                        {stage.concept_paper.title}
                                    </p>
                                </div>
                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <p className="text-xs font-medium text-gray-500 uppercase">
                                            Stage
                                        </p>
                                        <p className="mt-1 text-sm text-gray-900">
                                            {stage.stage_name}
                                        </p>
                                    </div>
                                    <div>
                                        <p className="text-xs font-medium text-gray-500 uppercase">
                                            Deadline
                                        </p>
                                        <p className="mt-1 text-sm text-gray-900">
                                            {formatDate(stage.deadline)}
                                        </p>
                                    </div>
                                </div>
                                <div className="pt-2">
                                    <Link
                                        href={`/concept-papers/${stage.concept_paper.id}`}
                                        className="inline-flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                                    >
                                        Review Paper
                                    </Link>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </>
    );

    // Render admin dashboard
    const renderAdminDashboard = () => (
        <>
            {/* Quick Actions for Admin */}
            <div className="mb-8 flex flex-wrap gap-3">
                <Link
                    href="/admin/users"
                    className="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg
                        className="h-5 w-5 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                        />
                    </svg>
                    Manage Users
                </Link>
                <Link
                    href="/admin/reports"
                    className="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg
                        className="h-5 w-5 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    View Reports
                </Link>
                <Link
                    href="/concept-papers"
                    className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                >
                    <svg
                        className="h-5 w-5 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            strokeLinecap="round"
                            strokeLinejoin="round"
                            strokeWidth={2}
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                        />
                    </svg>
                    View All Papers
                </Link>
            </div>

            {/* Overdue Alert for Admin */}
            {dashboard_data.system_statistics.overdue_count > 0 && (
                <div className="mb-8 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div className="flex items-start sm:items-center">
                            <svg
                                className="h-6 w-6 text-red-600 mr-3 flex-shrink-0"
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
                            <div>
                                <h3 className="text-sm font-medium text-red-800">
                                    {
                                        dashboard_data.system_statistics
                                            .overdue_count
                                    }{" "}
                                    Overdue Stage
                                    {dashboard_data.system_statistics
                                        .overdue_count !== 1
                                        ? "s"
                                        : ""}{" "}
                                    System-Wide
                                </h3>
                                <p className="text-xs text-red-600 mt-0.5">
                                    Multiple papers have stages past their
                                    deadlines
                                </p>
                            </div>
                        </div>
                        <Link
                            href="/concept-papers?filter=overdue"
                            className="inline-flex items-center justify-center px-3 py-2 border border-red-300 shadow-sm text-sm leading-4 font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                        >
                            View All
                        </Link>
                    </div>
                </div>
            )}

            {/* Stats Cards */}
            <div className="grid grid-cols-1 gap-5 sm:grid-cols-4 mb-8">
                <StatsCard
                    title="Total Papers"
                    value={dashboard_data.system_statistics.total_papers}
                    color="blue"
                    icon={
                        <svg
                            className="h-6 w-6 text-blue-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                            />
                        </svg>
                    }
                />
                <StatsCard
                    title="In Progress"
                    value={
                        dashboard_data.system_statistics.by_status.in_progress
                    }
                    color="yellow"
                    icon={
                        <svg
                            className="h-6 w-6 text-yellow-600"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    }
                />
                <StatsCard
                    title="Completed"
                    value={dashboard_data.system_statistics.by_status.completed}
                    color="green"
                    icon={
                        <svg
                            className="h-6 w-6 text-green-600"
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
                    }
                />
                <StatsCard
                    title="Overdue"
                    value={dashboard_data.system_statistics.overdue_count || 0}
                    color="red"
                    icon={
                        <svg
                            className="h-6 w-6 text-red-600"
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
                    }
                />
            </div>

            {/* Recent Papers */}
            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div className="p-6 border-b border-gray-200">
                    <h3 className="text-lg font-medium text-gray-900">
                        Recent Concept Papers
                    </h3>
                </div>
                <div className="overflow-x-auto">
                    <table className="min-w-full divide-y divide-gray-200">
                        <thead className="bg-gray-50">
                            <tr>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tracking Number
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Title
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Requisitioner
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Current Stage
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status
                                </th>
                                <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody className="bg-white divide-y divide-gray-200">
                            {dashboard_data.recent_papers.map((paper) => (
                                <tr
                                    key={paper.id}
                                    className={
                                        paper.is_overdue ? "bg-red-50" : ""
                                    }
                                >
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {paper.tracking_number}
                                    </td>
                                    <td className="px-6 py-4 text-sm text-gray-900">
                                        {paper.title}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {paper.requisitioner.name}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {paper.current_stage
                                            ? paper.current_stage.stage_name
                                            : "N/A"}
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap">
                                        <StatusBadge
                                            status={
                                                paper.is_overdue
                                                    ? "overdue"
                                                    : paper.status
                                            }
                                        />
                                    </td>
                                    <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <Link
                                            href={`/concept-papers/${paper.id}`}
                                            className="text-blue-600 hover:text-blue-900"
                                        >
                                            View
                                        </Link>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </>
    );

    return (
        <AuthenticatedLayout
            header={
                <div className="flex justify-between items-center">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Dashboard - {getRoleDisplayName(user.role)}
                    </h2>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Role-specific dashboard content */}
                    {dashboard_data.role_type === "requisitioner" &&
                        renderRequisitionerDashboard()}
                    {dashboard_data.role_type === "approver" &&
                        renderApproverDashboard()}
                    {dashboard_data.role_type === "admin" &&
                        renderAdminDashboard()}

                    {/* Help & Resources Section */}
                    <div className="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-8">
                        <div className="flex items-start space-x-4">
                            <div className="flex-shrink-0">
                                <svg
                                    className="h-8 w-8 text-blue-600"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                    />
                                </svg>
                            </div>
                            <div className="flex-1">
                                <h3 className="text-lg font-semibold text-gray-900 mb-2">
                                    Help & Resources
                                </h3>
                                <p className="text-sm text-gray-600 mb-4">
                                    Need help using the system? Check out our
                                    comprehensive user guide for step-by-step
                                    instructions and tips.
                                </p>
                                <Link
                                    href={route("user-guide")}
                                    className="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <svg
                                        className="h-4 w-4 mr-2"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                        />
                                    </svg>
                                    View User Guide
                                </Link>
                            </div>
                        </div>
                    </div>

                    {/* Overdue Items Alert */}
                    {overdue_items.length > 0 && (
                        <div className="bg-red-50 border-l-4 border-red-400 p-4 mb-8">
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
                                    <h3 className="text-sm font-medium text-red-800">
                                        Overdue Items ({overdue_items.length})
                                    </h3>
                                    <div className="mt-2 text-sm text-red-700">
                                        <ul className="list-disc pl-5 space-y-1">
                                            {overdue_items
                                                .slice(0, 3)
                                                .map((item) => (
                                                    <li key={item.id}>
                                                        <Link
                                                            href={`/concept-papers/${item.concept_paper.id}`}
                                                            className="underline hover:text-red-900"
                                                        >
                                                            {
                                                                item
                                                                    .concept_paper
                                                                    .tracking_number
                                                            }{" "}
                                                            - {item.stage_name}(
                                                            {item.days_overdue}{" "}
                                                            days overdue)
                                                        </Link>
                                                    </li>
                                                ))}
                                        </ul>
                                        {overdue_items.length > 3 && (
                                            <p className="mt-2">
                                                <Link
                                                    href="/concept-papers?filter=overdue"
                                                    className="font-medium underline hover:text-red-900"
                                                >
                                                    View all{" "}
                                                    {overdue_items.length}{" "}
                                                    overdue items →
                                                </Link>
                                            </p>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {/* Recent Activity */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 border-b border-gray-200">
                            <h3 className="text-lg font-medium text-gray-900">
                                Recent Activity
                            </h3>
                        </div>
                        <div className="p-6">
                            {recent_activity.length === 0 ? (
                                <p className="text-sm text-gray-500">
                                    No recent activity.
                                </p>
                            ) : (
                                <div className="flow-root">
                                    <ul className="-mb-8">
                                        {recent_activity.map(
                                            (activity, index) => (
                                                <li key={activity.id}>
                                                    <div className="relative pb-8">
                                                        {index !==
                                                            recent_activity.length -
                                                            1 && (
                                                                <span
                                                                    className="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200"
                                                                    aria-hidden="true"
                                                                />
                                                            )}
                                                        <div className="relative flex space-x-3">
                                                            <div>
                                                                <span className="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center ring-8 ring-white">
                                                                    <svg
                                                                        className="h-5 w-5 text-white"
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
                                                            <div className="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                                <div>
                                                                    <p className="text-sm text-gray-500">
                                                                        <span className="font-medium text-gray-900">
                                                                            {
                                                                                activity
                                                                                    .user
                                                                                    .name
                                                                            }
                                                                        </span>{" "}
                                                                        {
                                                                            activity.action
                                                                        }{" "}
                                                                        {activity.stage_name && (
                                                                            <span className="font-medium">
                                                                                {
                                                                                    activity.stage_name
                                                                                }
                                                                            </span>
                                                                        )}
                                                                        {
                                                                            " for "
                                                                        }
                                                                        <Link
                                                                            href={`/concept-papers/${activity.concept_paper.id}`}
                                                                            className="font-medium text-blue-600 hover:text-blue-900"
                                                                        >
                                                                            {
                                                                                activity
                                                                                    .concept_paper
                                                                                    .tracking_number
                                                                            }
                                                                        </Link>
                                                                    </p>
                                                                    {activity.remarks && (
                                                                        <p className="mt-1 text-sm text-gray-600 italic">
                                                                            "
                                                                            {
                                                                                activity.remarks
                                                                            }
                                                                            "
                                                                        </p>
                                                                    )}
                                                                </div>
                                                                <div className="text-right text-sm whitespace-nowrap text-gray-500">
                                                                    {formatDateTime(
                                                                        activity.created_at
                                                                    )}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </li>
                                            )
                                        )}
                                    </ul>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

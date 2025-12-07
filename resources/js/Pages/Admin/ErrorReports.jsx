import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm, router } from "@inertiajs/react";
import { useState } from "react";
import {
    ExclamationTriangleIcon,
    CheckCircleIcon,
    ClockIcon,
    XCircleIcon,
    FunnelIcon,
    TrashIcon,
    EyeIcon,
} from "@heroicons/react/24/outline";

export default function ErrorReports({ reports, stats, currentFilter }) {
    const [selectedReport, setSelectedReport] = useState(null);
    const { data, setData, put, processing } = useForm({
        status: "",
        admin_notes: "",
    });

    const formatDate = (dateString) => {
        if (!dateString) return "N/A";
        return new Date(dateString).toLocaleString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    const getStatusBadge = (status) => {
        const styles = {
            pending:
                "bg-yellow-100 text-yellow-800 border-yellow-200",
            in_progress:
                "bg-blue-100 text-blue-800 border-blue-200",
            resolved:
                "bg-green-100 text-green-800 border-green-200",
            dismissed:
                "bg-gray-100 text-gray-800 border-gray-200",
        };

        const labels = {
            pending: "Pending",
            in_progress: "In Progress",
            resolved: "Resolved",
            dismissed: "Dismissed",
        };

        return (
            <span
                className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${styles[status]}`}
            >
                {labels[status]}
            </span>
        );
    };

    const handleFilter = (status) => {
        router.get(
            route("admin.error-reports.index"),
            { status },
            { preserveState: true }
        );
    };

    const handleStatusUpdate = (report) => {
        put(route("admin.error-reports.update", report.id), {
            preserveScroll: true,
            onSuccess: () => {
                setSelectedReport(null);
            },
        });
    };

    const handleDelete = (report) => {
        if (confirm("Are you sure you want to delete this error report?")) {
            router.delete(route("admin.error-reports.destroy", report.id), {
                preserveScroll: true,
            });
        }
    };

    const openDetailModal = (report) => {
        setSelectedReport(report);
        setData({
            status: report.status,
            admin_notes: report.admin_notes || "",
        });
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Error Reports
                    </h2>
                    <Link
                        href={route("dashboard")}
                        className="text-indigo-600 hover:text-indigo-900 text-sm font-medium"
                    >
                        ← Back to Dashboard
                    </Link>
                </div>
            }
        >
            <Head title="Error Reports" />

            <div className="py-6 sm:py-12">
                <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    {/* Stats Cards */}
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div className="bg-white rounded-lg shadow-sm p-4">
                            <div className="flex items-center">
                                <ExclamationTriangleIcon className="h-8 w-8 text-yellow-500" />
                                <div className="ml-3">
                                    <p className="text-2xl font-bold text-gray-900">
                                        {stats.pending}
                                    </p>
                                    <p className="text-sm text-gray-500">
                                        Pending
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm p-4">
                            <div className="flex items-center">
                                <ClockIcon className="h-8 w-8 text-blue-500" />
                                <div className="ml-3">
                                    <p className="text-2xl font-bold text-gray-900">
                                        {stats.in_progress}
                                    </p>
                                    <p className="text-sm text-gray-500">
                                        In Progress
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm p-4">
                            <div className="flex items-center">
                                <CheckCircleIcon className="h-8 w-8 text-green-500" />
                                <div className="ml-3">
                                    <p className="text-2xl font-bold text-gray-900">
                                        {stats.resolved}
                                    </p>
                                    <p className="text-sm text-gray-500">
                                        Resolved
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div className="bg-white rounded-lg shadow-sm p-4">
                            <div className="flex items-center">
                                <XCircleIcon className="h-8 w-8 text-gray-500" />
                                <div className="ml-3">
                                    <p className="text-2xl font-bold text-gray-900">
                                        {stats.total}
                                    </p>
                                    <p className="text-sm text-gray-500">
                                        Total
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Filter Buttons */}
                    <div className="mb-6 flex flex-wrap gap-2">
                        <button
                            onClick={() => handleFilter("all")}
                            className={`inline-flex items-center px-4 py-2 rounded-md text-sm font-medium ${currentFilter === "all"
                                    ? "bg-indigo-600 text-white"
                                    : "bg-white text-gray-700 hover:bg-gray-50 border border-gray-300"
                                }`}
                        >
                            <FunnelIcon className="h-4 w-4 mr-2" />
                            All
                        </button>
                        <button
                            onClick={() => handleFilter("pending")}
                            className={`inline-flex items-center px-4 py-2 rounded-md text-sm font-medium ${currentFilter === "pending"
                                    ? "bg-yellow-600 text-white"
                                    : "bg-white text-gray-700 hover:bg-gray-50 border border-gray-300"
                                }`}
                        >
                            Pending
                        </button>
                        <button
                            onClick={() => handleFilter("in_progress")}
                            className={`inline-flex items-center px-4 py-2 rounded-md text-sm font-medium ${currentFilter === "in_progress"
                                    ? "bg-blue-600 text-white"
                                    : "bg-white text-gray-700 hover:bg-gray-50 border border-gray-300"
                                }`}
                        >
                            In Progress
                        </button>
                        <button
                            onClick={() => handleFilter("resolved")}
                            className={`inline-flex items-center px-4 py-2 rounded-md text-sm font-medium ${currentFilter === "resolved"
                                    ? "bg-green-600 text-white"
                                    : "bg-white text-gray-700 hover:bg-gray-50 border border-gray-300"
                                }`}
                        >
                            Resolved
                        </button>
                    </div>

                    {/* Reports Table */}
                    <div className="bg-white shadow-sm rounded-lg overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Title
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Reported By
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th className="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {reports.data.length === 0 ? (
                                        <tr>
                                            <td
                                                colSpan="5"
                                                className="px-6 py-12 text-center text-gray-500"
                                            >
                                                No error reports found.
                                            </td>
                                        </tr>
                                    ) : (
                                        reports.data.map((report) => (
                                            <tr
                                                key={report.id}
                                                className="hover:bg-gray-50"
                                            >
                                                <td className="px-6 py-4">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {report.title}
                                                    </div>
                                                    <div className="text-sm text-gray-500 truncate max-w-xs">
                                                        {report.description.substring(
                                                            0,
                                                            50
                                                        )}
                                                        ...
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="text-sm text-gray-900">
                                                        {report.user?.name}
                                                    </div>
                                                    <div className="text-sm text-gray-500 capitalize">
                                                        {report.user?.role?.replace(
                                                            "_",
                                                            " "
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4">
                                                    {getStatusBadge(
                                                        report.status
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 text-sm text-gray-500">
                                                    {formatDate(
                                                        report.created_at
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 text-right text-sm font-medium">
                                                    <button
                                                        onClick={() =>
                                                            openDetailModal(
                                                                report
                                                            )
                                                        }
                                                        className="text-indigo-600 hover:text-indigo-900 mr-3"
                                                    >
                                                        <EyeIcon className="h-5 w-5" />
                                                    </button>
                                                    <button
                                                        onClick={() =>
                                                            handleDelete(report)
                                                        }
                                                        className="text-red-600 hover:text-red-900"
                                                    >
                                                        <TrashIcon className="h-5 w-5" />
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {reports.links && reports.links.length > 3 && (
                            <div className="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                                <nav className="flex justify-center">
                                    <ul className="flex gap-1">
                                        {reports.links.map((link, index) => (
                                            <li key={index}>
                                                {link.url ? (
                                                    <Link
                                                        href={link.url}
                                                        className={`px-3 py-2 text-sm rounded-md ${link.active
                                                                ? "bg-indigo-600 text-white"
                                                                : "bg-white text-gray-700 hover:bg-gray-50 border border-gray-300"
                                                            }`}
                                                        dangerouslySetInnerHTML={{
                                                            __html: link.label,
                                                        }}
                                                    />
                                                ) : (
                                                    <span
                                                        className="px-3 py-2 text-sm text-gray-400"
                                                        dangerouslySetInnerHTML={{
                                                            __html: link.label,
                                                        }}
                                                    />
                                                )}
                                            </li>
                                        ))}
                                    </ul>
                                </nav>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Detail Modal */}
            {selectedReport && (
                <div className="fixed inset-0 z-50 overflow-y-auto">
                    <div className="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div
                            className="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                            onClick={() => setSelectedReport(null)}
                        />

                        <div className="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                            <div className="bg-white px-4 pt-5 pb-4 sm:p-6">
                                <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                    Error Report Details
                                </h3>

                                <div className="space-y-4">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Title
                                        </label>
                                        <p className="mt-1 text-sm text-gray-900">
                                            {selectedReport.title}
                                        </p>
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700">
                                            Description
                                        </label>
                                        <p className="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                                            {selectedReport.description}
                                        </p>
                                    </div>

                                    {selectedReport.steps_to_reproduce && (
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Steps to Reproduce
                                            </label>
                                            <p className="mt-1 text-sm text-gray-900 whitespace-pre-wrap">
                                                {
                                                    selectedReport.steps_to_reproduce
                                                }
                                            </p>
                                        </div>
                                    )}

                                    {selectedReport.error_message && (
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Error Message
                                            </label>
                                            <p className="mt-1 text-sm text-gray-900 font-mono bg-gray-50 p-2 rounded">
                                                {selectedReport.error_message}
                                            </p>
                                        </div>
                                    )}

                                    <div className="grid grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Page URL
                                            </label>
                                            <p className="mt-1 text-sm text-blue-600 truncate">
                                                {selectedReport.page_url}
                                            </p>
                                        </div>
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700">
                                                Reported By
                                            </label>
                                            <p className="mt-1 text-sm text-gray-900">
                                                {selectedReport.user?.name} (
                                                {selectedReport.user?.role})
                                            </p>
                                        </div>
                                    </div>

                                    <div className="border-t pt-4 mt-4">
                                        <div className="grid grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-sm font-medium text-gray-700">
                                                    Status
                                                </label>
                                                <select
                                                    value={data.status}
                                                    onChange={(e) =>
                                                        setData(
                                                            "status",
                                                            e.target.value
                                                        )
                                                    }
                                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                >
                                                    <option value="pending">
                                                        Pending
                                                    </option>
                                                    <option value="in_progress">
                                                        In Progress
                                                    </option>
                                                    <option value="resolved">
                                                        Resolved
                                                    </option>
                                                    <option value="dismissed">
                                                        Dismissed
                                                    </option>
                                                </select>
                                            </div>
                                        </div>

                                        <div className="mt-4">
                                            <label className="block text-sm font-medium text-gray-700">
                                                Admin Notes
                                            </label>
                                            <textarea
                                                value={data.admin_notes}
                                                onChange={(e) =>
                                                    setData(
                                                        "admin_notes",
                                                        e.target.value
                                                    )
                                                }
                                                rows="3"
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                placeholder="Add notes about this issue..."
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div className="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                <button
                                    type="button"
                                    onClick={() =>
                                        handleStatusUpdate(selectedReport)
                                    }
                                    disabled={processing}
                                    className="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50"
                                >
                                    {processing ? "Saving..." : "Save Changes"}
                                </button>
                                <button
                                    type="button"
                                    onClick={() => setSelectedReport(null)}
                                    className="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}

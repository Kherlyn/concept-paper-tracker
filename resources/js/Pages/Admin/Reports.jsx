import { useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import TextInput from "@/Components/TextInput";
import InputLabel from "@/Components/InputLabel";

export default function Reports({ statistics, stage_averages }) {
    const [dateFrom, setDateFrom] = useState("");
    const [dateTo, setDateTo] = useState("");
    const [statusFilter, setStatusFilter] = useState("");
    const [selectedPaperId, setSelectedPaperId] = useState("");

    const handleExportCsv = () => {
        const params = new URLSearchParams();
        if (dateFrom) params.append("date_from", dateFrom);
        if (dateTo) params.append("date_to", dateTo);
        if (statusFilter) params.append("status", statusFilter);

        window.location.href = `/admin/reports/csv?${params.toString()}`;
    };

    const handleGeneratePdf = () => {
        if (!selectedPaperId) {
            alert("Please enter a concept paper ID");
            return;
        }
        window.location.href = `/admin/reports/pdf/${selectedPaperId}`;
    };

    const formatNumber = (num) => {
        return num ? num.toLocaleString() : "0";
    };

    const formatPercentage = (num) => {
        return num ? `${num}%` : "0%";
    };

    // Calculate pie chart data
    const pieChartData = [
        {
            label: "Pending",
            value: statistics.by_status.pending,
            color: "bg-yellow-500",
        },
        {
            label: "In Progress",
            value: statistics.by_status.in_progress,
            color: "bg-blue-500",
        },
        {
            label: "Completed",
            value: statistics.by_status.completed,
            color: "bg-green-500",
        },
        {
            label: "Returned",
            value: statistics.by_status.returned,
            color: "bg-red-500",
        },
    ];

    const total = pieChartData.reduce((sum, item) => sum + item.value, 0);

    return (
        <AuthenticatedLayout>
            <Head title="Reports & Analytics" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    {/* Header */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h2 className="text-2xl font-semibold text-gray-900">
                                Reports & Analytics
                            </h2>
                            <p className="mt-1 text-sm text-gray-600">
                                View statistics and generate reports for concept
                                papers
                            </p>
                        </div>
                    </div>

                    {/* Filter and Export Section */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Export Data
                            </h3>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* CSV Export */}
                                <div className="border border-gray-200 rounded-lg p-4">
                                    <h4 className="font-medium text-gray-900 mb-4">
                                        Export to CSV
                                    </h4>
                                    <div className="space-y-3">
                                        <div>
                                            <InputLabel
                                                htmlFor="date_from"
                                                value="Date From"
                                            />
                                            <TextInput
                                                id="date_from"
                                                type="date"
                                                value={dateFrom}
                                                onChange={(e) =>
                                                    setDateFrom(e.target.value)
                                                }
                                                className="mt-1 block w-full"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel
                                                htmlFor="date_to"
                                                value="Date To"
                                            />
                                            <TextInput
                                                id="date_to"
                                                type="date"
                                                value={dateTo}
                                                onChange={(e) =>
                                                    setDateTo(e.target.value)
                                                }
                                                className="mt-1 block w-full"
                                            />
                                        </div>
                                        <div>
                                            <InputLabel
                                                htmlFor="status"
                                                value="Status"
                                            />
                                            <select
                                                id="status"
                                                value={statusFilter}
                                                onChange={(e) =>
                                                    setStatusFilter(
                                                        e.target.value
                                                    )
                                                }
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            >
                                                <option value="">
                                                    All Status
                                                </option>
                                                <option value="pending">
                                                    Pending
                                                </option>
                                                <option value="in_progress">
                                                    In Progress
                                                </option>
                                                <option value="completed">
                                                    Completed
                                                </option>
                                                <option value="returned">
                                                    Returned
                                                </option>
                                            </select>
                                        </div>
                                        <PrimaryButton
                                            onClick={handleExportCsv}
                                            className="w-full"
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
                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                                                />
                                            </svg>
                                            Export CSV
                                        </PrimaryButton>
                                    </div>
                                </div>

                                {/* PDF Export */}
                                <div className="border border-gray-200 rounded-lg p-4">
                                    <h4 className="font-medium text-gray-900 mb-4">
                                        Generate PDF Report
                                    </h4>
                                    <div className="space-y-3">
                                        <div>
                                            <InputLabel
                                                htmlFor="paper_id"
                                                value="Concept Paper ID"
                                            />
                                            <TextInput
                                                id="paper_id"
                                                type="number"
                                                value={selectedPaperId}
                                                onChange={(e) =>
                                                    setSelectedPaperId(
                                                        e.target.value
                                                    )
                                                }
                                                placeholder="Enter paper ID"
                                                className="mt-1 block w-full"
                                            />
                                            <p className="mt-1 text-xs text-gray-500">
                                                Enter the ID of the concept
                                                paper to generate a detailed PDF
                                                report
                                            </p>
                                        </div>
                                        <PrimaryButton
                                            onClick={handleGeneratePdf}
                                            className="w-full"
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
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"
                                                />
                                            </svg>
                                            Generate PDF
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Statistics Dashboard */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-6">
                                Overall Statistics
                            </h3>

                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                                <div className="bg-blue-50 rounded-lg p-4">
                                    <div className="text-sm font-medium text-blue-600">
                                        Total Papers
                                    </div>
                                    <div className="mt-2 text-3xl font-bold text-blue-900">
                                        {formatNumber(statistics.total_papers)}
                                    </div>
                                </div>
                                <div className="bg-green-50 rounded-lg p-4">
                                    <div className="text-sm font-medium text-green-600">
                                        Completed
                                    </div>
                                    <div className="mt-2 text-3xl font-bold text-green-900">
                                        {formatNumber(
                                            statistics.by_status.completed
                                        )}
                                    </div>
                                    <div className="text-xs text-green-600 mt-1">
                                        {formatPercentage(
                                            statistics.completion_rate
                                        )}{" "}
                                        completion rate
                                    </div>
                                </div>
                                <div className="bg-yellow-50 rounded-lg p-4">
                                    <div className="text-sm font-medium text-yellow-600">
                                        Avg Processing Time
                                    </div>
                                    <div className="mt-2 text-3xl font-bold text-yellow-900">
                                        {statistics.avg_processing_days}
                                    </div>
                                    <div className="text-xs text-yellow-600 mt-1">
                                        days
                                    </div>
                                </div>
                                <div className="bg-red-50 rounded-lg p-4">
                                    <div className="text-sm font-medium text-red-600">
                                        Overdue
                                    </div>
                                    <div className="mt-2 text-3xl font-bold text-red-900">
                                        {formatNumber(
                                            statistics.overdue_papers
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Pie Chart - Papers by Status */}
                            <div className="mt-8">
                                <h4 className="text-md font-medium text-gray-900 mb-4">
                                    Papers by Status
                                </h4>
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {/* Simple Bar Chart */}
                                    <div className="space-y-3">
                                        {pieChartData.map((item, index) => (
                                            <div key={index}>
                                                <div className="flex justify-between text-sm mb-1">
                                                    <span className="text-gray-700">
                                                        {item.label}
                                                    </span>
                                                    <span className="font-medium text-gray-900">
                                                        {item.value} (
                                                        {total > 0
                                                            ? Math.round(
                                                                  (item.value /
                                                                      total) *
                                                                      100
                                                              )
                                                            : 0}
                                                        %)
                                                    </span>
                                                </div>
                                                <div className="w-full bg-gray-200 rounded-full h-3">
                                                    <div
                                                        className={`${item.color} h-3 rounded-full transition-all duration-300`}
                                                        style={{
                                                            width: `${
                                                                total > 0
                                                                    ? (item.value /
                                                                          total) *
                                                                      100
                                                                    : 0
                                                            }%`,
                                                        }}
                                                    ></div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>

                                    {/* Legend */}
                                    <div className="flex items-center justify-center">
                                        <div className="space-y-2">
                                            {pieChartData.map((item, index) => (
                                                <div
                                                    key={index}
                                                    className="flex items-center space-x-3"
                                                >
                                                    <div
                                                        className={`w-4 h-4 rounded ${item.color}`}
                                                    ></div>
                                                    <span className="text-sm text-gray-700">
                                                        {item.label}:{" "}
                                                        <span className="font-medium">
                                                            {item.value}
                                                        </span>
                                                    </span>
                                                </div>
                                            ))}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Stage Averages Table */}
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <h3 className="text-lg font-medium text-gray-900 mb-4">
                                Average Processing Time by Stage
                            </h3>
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Stage
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Assigned Role
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Max Days
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Avg Processing Time
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total Count
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Completed
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Overdue
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Completion Rate
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {stage_averages.map((stage, index) => (
                                            <tr
                                                key={index}
                                                className="hover:bg-gray-50"
                                            >
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    {stage.stage_name}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                                    {stage.assigned_role.replace(
                                                        "_",
                                                        " "
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {stage.max_days}{" "}
                                                    {stage.max_days === 1
                                                        ? "day"
                                                        : "days"}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {stage.avg_processing_days >
                                                    0
                                                        ? `${stage.avg_processing_days}d ${stage.avg_processing_hours_remainder}h`
                                                        : "N/A"}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {stage.total_count}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {stage.completed_count}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {stage.overdue_count > 0 ? (
                                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                            {
                                                                stage.overdue_count
                                                            }
                                                        </span>
                                                    ) : (
                                                        <span className="text-gray-400">
                                                            0
                                                        </span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    <div className="flex items-center">
                                                        <div className="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                                            <div
                                                                className="bg-blue-600 h-2 rounded-full"
                                                                style={{
                                                                    width: `${stage.completion_rate}%`,
                                                                }}
                                                            ></div>
                                                        </div>
                                                        <span>
                                                            {
                                                                stage.completion_rate
                                                            }
                                                            %
                                                        </span>
                                                    </div>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {/* Monthly Trends Chart */}
                    {statistics.monthly_trends &&
                        Object.keys(statistics.monthly_trends).length > 0 && (
                            <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                                <div className="p-6">
                                    <h3 className="text-lg font-medium text-gray-900 mb-4">
                                        Monthly Submission Trends (Last 12
                                        Months)
                                    </h3>
                                    <div className="space-y-3">
                                        {Object.entries(
                                            statistics.monthly_trends
                                        ).map(([month, count]) => {
                                            const maxCount = Math.max(
                                                ...Object.values(
                                                    statistics.monthly_trends
                                                )
                                            );
                                            const percentage =
                                                maxCount > 0
                                                    ? (count / maxCount) * 100
                                                    : 0;

                                            return (
                                                <div key={month}>
                                                    <div className="flex justify-between text-sm mb-1">
                                                        <span className="text-gray-700">
                                                            {month}
                                                        </span>
                                                        <span className="font-medium text-gray-900">
                                                            {count} papers
                                                        </span>
                                                    </div>
                                                    <div className="w-full bg-gray-200 rounded-full h-3">
                                                        <div
                                                            className="bg-indigo-600 h-3 rounded-full transition-all duration-300"
                                                            style={{
                                                                width: `${percentage}%`,
                                                            }}
                                                        ></div>
                                                    </div>
                                                </div>
                                            );
                                        })}
                                    </div>
                                </div>
                            </div>
                        )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

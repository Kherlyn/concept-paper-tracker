export default function StatusBadge({ status }) {
    const statusConfig = {
        pending: {
            label: "Pending",
            classes: "bg-yellow-100 text-yellow-800 border-yellow-200",
            ariaLabel: "Status: Pending",
        },
        in_progress: {
            label: "In Progress",
            classes: "bg-blue-100 text-blue-800 border-blue-200",
            ariaLabel: "Status: In Progress",
        },
        completed: {
            label: "Completed",
            classes: "bg-green-100 text-green-800 border-green-200",
            ariaLabel: "Status: Completed",
        },
        returned: {
            label: "Returned",
            classes: "bg-orange-100 text-orange-800 border-orange-200",
            ariaLabel: "Status: Returned",
        },
        overdue: {
            label: "Overdue",
            classes: "bg-red-100 text-red-800 border-red-200",
            ariaLabel: "Status: Overdue - Requires immediate attention",
        },
    };

    const config = statusConfig[status] || statusConfig.pending;

    return (
        <span
            className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border ${config.classes}`}
            role="status"
            aria-label={config.ariaLabel}
        >
            {config.label}
        </span>
    );
}

import {
    CheckCircleIcon,
    ClockIcon,
    UserGroupIcon,
} from "@heroicons/react/24/outline";

/**
 * WorkflowVisualization Component
 *
 * A reusable component that displays the 9-step workflow process with visual connections,
 * stage descriptions, and time estimates. Can be used in both the landing page and user guide.
 *
 * @param {Object} props
 * @param {Array} props.stages - Array of workflow stages (optional, uses default if not provided)
 * @param {boolean} props.compact - Whether to use compact layout (default: false)
 * @param {boolean} props.showRoles - Whether to show role information (default: true)
 * @param {boolean} props.showDescriptions - Whether to show stage descriptions (default: true)
 * @param {string} props.variant - Visual variant: 'default', 'minimal', or 'detailed' (default: 'default')
 */

const defaultStages = [
    {
        number: 1,
        name: "SPS Review",
        role: "School Principal/Supervisor",
        maxDays: 1,
        description: "Initial review and approval of the concept paper",
    },
    {
        number: 2,
        name: "VP Acad Review",
        role: "VP Academic Affairs",
        maxDays: 3,
        description: "Academic review and approval",
    },
    {
        number: 3,
        name: "Auditing Review",
        role: "Auditor",
        maxDays: 3,
        description: "Initial audit review for compliance",
    },
    {
        number: 4,
        name: "Acad Copy Distribution",
        role: "VP Academic Affairs",
        maxDays: 1,
        description: "Distribute academic copy to relevant parties",
    },
    {
        number: 5,
        name: "Auditing Copy Distribution",
        role: "Auditor",
        maxDays: 1,
        description: "Distribute auditing copy for records",
    },
    {
        number: 6,
        name: "Voucher Preparation",
        role: "Accounting",
        maxDays: 1,
        description: "Prepare payment voucher with documentation",
    },
    {
        number: 7,
        name: "Audit & Countersign",
        role: "Auditor",
        maxDays: 1,
        description: "Final audit review and countersigning",
    },
    {
        number: 8,
        name: "Cheque Preparation",
        role: "Accounting",
        maxDays: 4,
        description: "Process payment and prepare cheque",
    },
    {
        number: 9,
        name: "Budget Release",
        role: "Accounting",
        maxDays: 1,
        description: "Release budget and complete the process",
    },
];

function WorkflowStage({
    stage,
    index,
    isLast,
    showRoles,
    showDescriptions,
    variant,
}) {
    const isMinimal = variant === "minimal";
    const isDetailed = variant === "detailed";

    return (
        <div className="flex items-start">
            {/* Stage Number and Connector */}
            <div className="flex flex-col items-center mr-4">
                <div
                    className={`flex items-center justify-center ${
                        isMinimal ? "w-8 h-8" : "w-10 h-10"
                    } bg-indigo-600 text-white rounded-full font-bold ${
                        isMinimal ? "text-sm" : "text-base"
                    } shadow-md`}
                >
                    {stage.number || index + 1}
                </div>
                {!isLast && (
                    <div
                        className="w-0.5 bg-gradient-to-b from-indigo-600 to-indigo-300 mt-2"
                        style={{ minHeight: isMinimal ? "40px" : "60px" }}
                    ></div>
                )}
            </div>

            {/* Stage Content */}
            <div className={`flex-1 ${isLast ? "" : "pb-8"}`}>
                <div
                    className={`bg-white ${
                        isMinimal ? "p-3" : "p-4"
                    } rounded-lg shadow-sm border border-gray-100 hover:shadow-md transition-shadow`}
                >
                    {/* Stage Header */}
                    <div className="flex justify-between items-start mb-2">
                        <h4
                            className={`font-semibold text-gray-900 ${
                                isMinimal ? "text-sm" : "text-base"
                            }`}
                        >
                            {stage.name}
                        </h4>
                        <div className="flex items-center text-sm font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded">
                            <ClockIcon className="h-4 w-4 mr-1" />
                            <span>
                                {stage.maxDays}{" "}
                                {stage.maxDays === 1 ? "day" : "days"}
                            </span>
                        </div>
                    </div>

                    {/* Role Information */}
                    {showRoles && stage.role && (
                        <div className="flex items-center text-sm text-gray-600 mb-1">
                            <UserGroupIcon className="h-4 w-4 mr-1 text-gray-400" />
                            <span>{stage.role}</span>
                        </div>
                    )}

                    {/* Stage Description */}
                    {showDescriptions && stage.description && (
                        <p
                            className={`text-sm text-gray-500 ${
                                isDetailed ? "mt-2" : ""
                            }`}
                        >
                            {stage.description}
                        </p>
                    )}

                    {/* Additional Details for Detailed Variant */}
                    {isDetailed && stage.details && (
                        <div className="mt-3 pt-3 border-t border-gray-100">
                            <ul className="text-xs text-gray-600 space-y-1">
                                {stage.details.map((detail, idx) => (
                                    <li key={idx} className="flex items-start">
                                        <span className="text-indigo-600 mr-2">
                                            •
                                        </span>
                                        <span>{detail}</span>
                                    </li>
                                ))}
                            </ul>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}

export default function WorkflowVisualization({
    stages = defaultStages,
    compact = false,
    showRoles = true,
    showDescriptions = true,
    variant = "default",
    className = "",
}) {
    // Calculate total processing time
    const totalDays = stages.reduce(
        (sum, stage) => sum + (stage.maxDays || 0),
        0
    );

    return (
        <div className={className}>
            {/* Summary Information */}
            {!compact && (
                <div className="mb-8 text-center">
                    <div className="inline-flex items-center px-4 py-2 bg-green-50 text-green-700 rounded-lg">
                        <CheckCircleIcon className="h-5 w-5 mr-2" />
                        <span className="font-medium">
                            Total processing time: {totalDays} business days
                        </span>
                    </div>
                    <p className="mt-3 text-sm text-gray-600">
                        Each stage must be completed before the next can begin
                    </p>
                </div>
            )}

            {/* Workflow Stages */}
            <div className={compact ? "space-y-2" : ""}>
                {stages.map((stage, index) => (
                    <WorkflowStage
                        key={stage.number || index}
                        stage={stage}
                        index={index}
                        isLast={index === stages.length - 1}
                        showRoles={showRoles}
                        showDescriptions={showDescriptions}
                        variant={variant}
                    />
                ))}
            </div>

            {/* Footer Information */}
            {variant === "detailed" && (
                <div className="mt-8 p-4 bg-blue-50 rounded-lg">
                    <h4 className="font-semibold text-blue-900 mb-2">
                        Important Notes
                    </h4>
                    <ul className="text-sm text-blue-800 space-y-1">
                        <li>
                            • Papers can be returned to previous stages if
                            issues are found
                        </li>
                        <li>
                            • Overdue stages are highlighted and notifications
                            are sent
                        </li>
                        <li>
                            • Urgent requests may receive priority processing
                        </li>
                        <li>• All actions are tracked in the audit trail</li>
                    </ul>
                </div>
            )}
        </div>
    );
}

export default function WorkflowTimeline({ stages, currentStage }) {
    const getStageStatus = (stage) => {
        if (stage.status === "completed") return "completed";
        if (stage.status === "returned") return "returned";
        if (stage.id === currentStage?.id) return "current";
        if (stage.stage_order < currentStage?.stage_order) return "completed";
        return "pending";
    };

    const getStatusColor = (status) => {
        switch (status) {
            case "completed":
                return "bg-green-500 border-green-500";
            case "current":
                return "bg-blue-500 border-blue-500 ring-4 ring-blue-100";
            case "returned":
                return "bg-orange-500 border-orange-500";
            case "pending":
            default:
                return "bg-gray-300 border-gray-300";
        }
    };

    const getLineColor = (index) => {
        if (index >= stages.length - 1) return "";

        const currentStageStatus = getStageStatus(stages[index]);
        const nextStageStatus = getStageStatus(stages[index + 1]);

        if (
            currentStageStatus === "completed" &&
            (nextStageStatus === "completed" || nextStageStatus === "current")
        ) {
            return "bg-green-500";
        }
        return "bg-gray-300";
    };

    return (
        <div className="py-4 sm:py-6">
            <div className="flow-root">
                <ul className="-mb-8" role="list" aria-label="Workflow stages">
                    {stages.map((stage, index) => {
                        const status = getStageStatus(stage);
                        const isLast = index === stages.length - 1;

                        return (
                            <li key={stage.id}>
                                <div className="relative pb-8">
                                    {!isLast && (
                                        <span
                                            className={`absolute left-3 sm:left-4 top-4 -ml-px h-full w-0.5 ${getLineColor(
                                                index
                                            )}`}
                                            aria-hidden="true"
                                        />
                                    )}
                                    <div className="relative flex space-x-2 sm:space-x-3">
                                        <div className="flex-shrink-0">
                                            <span
                                                className={`h-6 w-6 sm:h-8 sm:w-8 rounded-full flex items-center justify-center border-2 ${getStatusColor(
                                                    status
                                                )}`}
                                                aria-label={`Stage ${
                                                    index + 1
                                                } status: ${status}`}
                                            >
                                                {status === "completed" && (
                                                    <svg
                                                        className="h-4 w-4 sm:h-5 sm:w-5 text-white"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                        aria-hidden="true"
                                                    >
                                                        <path
                                                            fillRule="evenodd"
                                                            d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                            clipRule="evenodd"
                                                        />
                                                    </svg>
                                                )}
                                                {status === "current" && (
                                                    <span
                                                        className="h-2 w-2 sm:h-3 sm:w-3 rounded-full bg-white"
                                                        aria-hidden="true"
                                                    ></span>
                                                )}
                                                {status === "returned" && (
                                                    <svg
                                                        className="h-4 w-4 sm:h-5 sm:w-5 text-white"
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                        aria-hidden="true"
                                                    >
                                                        <path
                                                            fillRule="evenodd"
                                                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                                            clipRule="evenodd"
                                                        />
                                                    </svg>
                                                )}
                                            </span>
                                        </div>
                                        <div className="flex min-w-0 flex-1 flex-col pt-0.5 sm:pt-1.5">
                                            <div className="min-w-0 flex-1">
                                                <p
                                                    className={`text-xs sm:text-sm font-medium break-words ${
                                                        status === "current"
                                                            ? "text-gray-900"
                                                            : "text-gray-500"
                                                    }`}
                                                >
                                                    {stage.stage_name}
                                                </p>
                                                {stage.assigned_user && (
                                                    <p className="text-xs text-gray-500 mt-0.5">
                                                        Assigned to:{" "}
                                                        {
                                                            stage.assigned_user
                                                                .name
                                                        }
                                                    </p>
                                                )}
                                                {stage.remarks && (
                                                    <p className="text-xs text-gray-600 mt-1 italic break-words">
                                                        {stage.remarks}
                                                    </p>
                                                )}
                                            </div>
                                            <div className="mt-1 sm:mt-2 text-xs sm:text-sm text-gray-500">
                                                {stage.completed_at && (
                                                    <time
                                                        dateTime={
                                                            stage.completed_at
                                                        }
                                                    >
                                                        Completed:{" "}
                                                        {new Date(
                                                            stage.completed_at
                                                        ).toLocaleDateString()}
                                                    </time>
                                                )}
                                                {status === "current" &&
                                                    stage.deadline && (
                                                        <div className="text-xs text-gray-500 mt-0.5">
                                                            Due:{" "}
                                                            {new Date(
                                                                stage.deadline
                                                            ).toLocaleDateString()}
                                                        </div>
                                                    )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        );
                    })}
                </ul>
            </div>
        </div>
    );
}

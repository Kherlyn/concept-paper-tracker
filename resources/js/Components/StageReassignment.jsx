import { useState, useEffect } from "react";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import InputLabel from "@/Components/InputLabel";

export default function StageReassignment({
    isOpen,
    onClose,
    affectedPapers,
    userToDeactivate,
    onReassignmentComplete,
}) {
    const [assignments, setAssignments] = useState({});
    const [availableUsers, setAvailableUsers] = useState({});
    const [isLoading, setIsLoading] = useState(false);
    const [showConfirmation, setShowConfirmation] = useState(false);
    const [bulkAssignee, setBulkAssignee] = useState("");
    const [bulkRole, setBulkRole] = useState("");

    // Initialize assignments state when modal opens
    useEffect(() => {
        if (isOpen && affectedPapers.length > 0) {
            const initialAssignments = {};
            affectedPapers.forEach((paper) => {
                paper.stages.forEach((stage) => {
                    initialAssignments[stage.id] = "";
                });
            });
            setAssignments(initialAssignments);
            fetchAvailableUsers();
        }
    }, [isOpen, affectedPapers]);

    // Fetch available active users for reassignment
    const fetchAvailableUsers = async () => {
        try {
            const response = await fetch(
                "/admin/users?is_active=1&per_page=1000"
            );
            const data = await response.json();

            // Group users by role
            const usersByRole = {};
            data.data.forEach((user) => {
                if (user.id !== userToDeactivate?.id) {
                    if (!usersByRole[user.role]) {
                        usersByRole[user.role] = [];
                    }
                    usersByRole[user.role].push(user);
                }
            });

            setAvailableUsers(usersByRole);
        } catch (error) {
            console.error("Error fetching available users:", error);
        }
    };

    // Handle individual stage assignment
    const handleAssignmentChange = (stageId, userId) => {
        setAssignments((prev) => ({
            ...prev,
            [stageId]: userId,
        }));
    };

    // Handle bulk assignment
    const handleBulkAssignment = () => {
        if (!bulkAssignee || !bulkRole) return;

        const updatedAssignments = { ...assignments };
        affectedPapers.forEach((paper) => {
            paper.stages.forEach((stage) => {
                if (stage.assigned_role === bulkRole) {
                    updatedAssignments[stage.id] = bulkAssignee;
                }
            });
        });
        setAssignments(updatedAssignments);
        setBulkAssignee("");
        setBulkRole("");
    };

    // Check if all stages have been assigned
    const allStagesAssigned = () => {
        return Object.values(assignments).every((userId) => userId !== "");
    };

    // Handle reassignment submission
    const handleSubmit = () => {
        if (!allStagesAssigned()) {
            alert("Please assign all stages before proceeding.");
            return;
        }
        setShowConfirmation(true);
    };

    // Confirm and execute reassignment
    const confirmReassignment = async () => {
        setIsLoading(true);
        try {
            const reassignmentPromises = Object.entries(assignments).map(
                ([stageId, userId]) => {
                    return fetch(`/admin/workflow-stages/${stageId}/reassign`, {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                        },
                        body: JSON.stringify({ new_user_id: userId }),
                    });
                }
            );

            await Promise.all(reassignmentPromises);

            // Call the completion callback
            if (onReassignmentComplete) {
                onReassignmentComplete();
            }

            // Close modals
            setShowConfirmation(false);
            onClose();
        } catch (error) {
            console.error("Error during reassignment:", error);
            alert("An error occurred during reassignment. Please try again.");
        } finally {
            setIsLoading(false);
        }
    };

    // Get role label
    const getRoleLabel = (role) => {
        const roleLabels = {
            requisitioner: "Requisitioner",
            sps: "SPS",
            vp_acad: "VP Acad",
            auditor: "Auditor",
            accounting: "Accounting",
            admin: "Admin",
            senior_vp: "Senior VP",
        };
        return roleLabels[role] || role;
    };

    // Get unique roles from affected stages
    const getUniqueRoles = () => {
        const roles = new Set();
        affectedPapers.forEach((paper) => {
            paper.stages.forEach((stage) => {
                roles.add(stage.assigned_role);
            });
        });
        return Array.from(roles);
    };

    if (!isOpen) return null;

    return (
        <>
            {/* Main Reassignment Modal */}
            <Modal
                show={isOpen && !showConfirmation}
                onClose={onClose}
                maxWidth="4xl"
            >
                <div className="p-6">
                    <div className="flex items-start mb-4">
                        <div className="flex-shrink-0">
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
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"
                                />
                            </svg>
                        </div>
                        <div className="ml-3 flex-1">
                            <h3 className="text-lg font-medium text-gray-900">
                                Reassign Workflow Stages
                            </h3>
                            <p className="mt-1 text-sm text-gray-600">
                                {userToDeactivate?.name} has{" "}
                                {affectedPapers.length} concept paper(s) with
                                pending stages. Please reassign each stage to an
                                active user with the matching role.
                            </p>
                        </div>
                    </div>

                    {/* Bulk Assignment Section */}
                    <div className="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 className="text-sm font-medium text-gray-900 mb-3">
                            Bulk Assignment (Optional)
                        </h4>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <InputLabel
                                    htmlFor="bulk_role"
                                    value="Select Role"
                                />
                                <select
                                    id="bulk_role"
                                    value={bulkRole}
                                    onChange={(e) =>
                                        setBulkRole(e.target.value)
                                    }
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                >
                                    <option value="">-- Select Role --</option>
                                    {getUniqueRoles().map((role) => (
                                        <option key={role} value={role}>
                                            {getRoleLabel(role)}
                                        </option>
                                    ))}
                                </select>
                            </div>
                            <div>
                                <InputLabel
                                    htmlFor="bulk_assignee"
                                    value="Select User"
                                />
                                <select
                                    id="bulk_assignee"
                                    value={bulkAssignee}
                                    onChange={(e) =>
                                        setBulkAssignee(e.target.value)
                                    }
                                    disabled={!bulkRole}
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 disabled:bg-gray-100"
                                >
                                    <option value="">-- Select User --</option>
                                    {bulkRole &&
                                        availableUsers[bulkRole]?.map(
                                            (user) => (
                                                <option
                                                    key={user.id}
                                                    value={user.id}
                                                >
                                                    {user.name} ({user.email})
                                                </option>
                                            )
                                        )}
                                </select>
                            </div>
                            <div className="flex items-end">
                                <PrimaryButton
                                    onClick={handleBulkAssignment}
                                    disabled={!bulkAssignee || !bulkRole}
                                    className="w-full"
                                >
                                    Apply to All {getRoleLabel(bulkRole)} Stages
                                </PrimaryButton>
                            </div>
                        </div>
                    </div>

                    {/* Individual Stage Assignments */}
                    <div className="max-h-96 overflow-y-auto">
                        <h4 className="text-sm font-medium text-gray-900 mb-3">
                            Individual Stage Assignments
                        </h4>
                        <div className="space-y-4">
                            {affectedPapers.map((paper) => (
                                <div
                                    key={paper.id}
                                    className="bg-gray-50 rounded-lg p-4 border border-gray-200"
                                >
                                    <div className="mb-3">
                                        <h5 className="font-medium text-gray-900">
                                            {paper.title}
                                        </h5>
                                        <p className="text-sm text-gray-600">
                                            Tracking: {paper.tracking_number} |
                                            Requisitioner: {paper.requisitioner}
                                        </p>
                                    </div>

                                    <div className="space-y-3">
                                        {paper.stages.map((stage) => (
                                            <div
                                                key={stage.id}
                                                className="bg-white rounded p-3 border border-gray-200"
                                            >
                                                <div className="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                    <div>
                                                        <div className="flex items-center space-x-2 mb-1">
                                                            <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                {
                                                                    stage.stage_name
                                                                }
                                                            </span>
                                                            <span className="text-xs text-gray-500">
                                                                (
                                                                {getRoleLabel(
                                                                    stage.assigned_role
                                                                )}
                                                                )
                                                            </span>
                                                        </div>
                                                        <p className="text-xs text-gray-600">
                                                            Status:{" "}
                                                            {stage.status}
                                                        </p>
                                                    </div>
                                                    <div>
                                                        <InputLabel
                                                            htmlFor={`stage_${stage.id}`}
                                                            value="Reassign to"
                                                        />
                                                        <select
                                                            id={`stage_${stage.id}`}
                                                            value={
                                                                assignments[
                                                                    stage.id
                                                                ] || ""
                                                            }
                                                            onChange={(e) =>
                                                                handleAssignmentChange(
                                                                    stage.id,
                                                                    e.target
                                                                        .value
                                                                )
                                                            }
                                                            className={`mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 ${
                                                                !assignments[
                                                                    stage.id
                                                                ]
                                                                    ? "border-red-300"
                                                                    : ""
                                                            }`}
                                                            required
                                                        >
                                                            <option value="">
                                                                -- Select User
                                                                --
                                                            </option>
                                                            {availableUsers[
                                                                stage
                                                                    .assigned_role
                                                            ]?.map((user) => (
                                                                <option
                                                                    key={
                                                                        user.id
                                                                    }
                                                                    value={
                                                                        user.id
                                                                    }
                                                                >
                                                                    {user.name}{" "}
                                                                    (
                                                                    {user.email}
                                                                    )
                                                                </option>
                                                            ))}
                                                        </select>
                                                        {!assignments[
                                                            stage.id
                                                        ] && (
                                                            <p className="mt-1 text-xs text-red-600">
                                                                Required
                                                            </p>
                                                        )}
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>

                    {/* Action Buttons */}
                    <div className="mt-6 flex justify-end space-x-3">
                        <SecondaryButton onClick={onClose} disabled={isLoading}>
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            onClick={handleSubmit}
                            disabled={!allStagesAssigned() || isLoading}
                        >
                            Continue to Confirmation
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>

            {/* Confirmation Modal */}
            <Modal
                show={showConfirmation}
                onClose={() => setShowConfirmation(false)}
                maxWidth="md"
            >
                <div className="p-6">
                    <div className="flex items-start">
                        <div className="flex-shrink-0">
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
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <div className="ml-3 flex-1">
                            <h3 className="text-lg font-medium text-gray-900">
                                Confirm Stage Reassignment
                            </h3>
                            <div className="mt-2 text-sm text-gray-600">
                                <p>
                                    You are about to reassign{" "}
                                    {Object.keys(assignments).length} workflow
                                    stage(s) from{" "}
                                    <strong>{userToDeactivate?.name}</strong> to
                                    the selected users.
                                </p>
                                <p className="mt-2">This action will:</p>
                                <ul className="list-disc list-inside mt-1 space-y-1">
                                    <li>Update all stage assignments</li>
                                    <li>
                                        Record the changes in the audit trail
                                    </li>
                                    <li>
                                        Send notifications to newly assigned
                                        users
                                    </li>
                                    <li>
                                        Allow you to proceed with deactivating{" "}
                                        {userToDeactivate?.name}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end space-x-3">
                        <SecondaryButton
                            onClick={() => setShowConfirmation(false)}
                            disabled={isLoading}
                        >
                            Go Back
                        </SecondaryButton>
                        <PrimaryButton
                            onClick={confirmReassignment}
                            disabled={isLoading}
                        >
                            {isLoading
                                ? "Reassigning..."
                                : "Confirm Reassignment"}
                        </PrimaryButton>
                    </div>
                </div>
            </Modal>
        </>
    );
}

import Modal from "@/Components/Modal";
import InputLabel from "@/Components/InputLabel";
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import FileUpload from "@/Components/FileUpload";
import { useForm } from "@inertiajs/react";
import { useEffect } from "react";

export default function StageActionModal({
    isOpen,
    onClose,
    stage,
    action, // 'complete', 'return', or 'attachment'
    conceptPaperId,
}) {
    const { data, setData, post, processing, errors, reset, clearErrors } =
        useForm({
            remarks: "",
            attachment: null,
        });

    useEffect(() => {
        if (!isOpen) {
            reset();
            clearErrors();
        }
    }, [isOpen]);

    const handleSubmit = (e) => {
        e.preventDefault();

        if (action === "complete") {
            post(route("workflow-stages.complete", stage.id), {
                onSuccess: () => {
                    onClose();
                    reset();
                },
            });
        } else if (action === "return") {
            post(route("workflow-stages.return", stage.id), {
                onSuccess: () => {
                    onClose();
                    reset();
                },
            });
        } else if (action === "attachment") {
            post(route("workflow-stages.add-attachment", stage.id), {
                forceFormData: true,
                onSuccess: () => {
                    onClose();
                    reset();
                },
            });
        }
    };

    const handleFileUpload = (file) => {
        setData("attachment", file);
    };

    const getModalTitle = () => {
        switch (action) {
            case "complete":
                return "Complete Stage";
            case "return":
                return "Return to Previous Stage";
            case "attachment":
                return "Add Attachment";
            default:
                return "Stage Action";
        }
    };

    const getModalDescription = () => {
        switch (action) {
            case "complete":
                return `You are about to complete the "${stage?.stage_name}" stage. You can optionally add remarks about your review.`;
            case "return":
                return `You are about to return the concept paper to the previous stage. Please provide a reason for returning.`;
            case "attachment":
                return `Upload a supporting document for the "${stage?.stage_name}" stage. Only PDF files up to 10MB are allowed.`;
            default:
                return "";
        }
    };

    const isRemarksRequired = action === "return";

    return (
        <Modal show={isOpen} onClose={onClose} maxWidth="lg">
            <form onSubmit={handleSubmit}>
                <div className="p-4 sm:p-6">
                    <h2 className="text-base sm:text-lg font-medium text-gray-900">
                        {getModalTitle()}
                    </h2>

                    <p className="mt-2 sm:mt-3 text-xs sm:text-sm text-gray-600">
                        {getModalDescription()}
                    </p>

                    <div className="mt-4 sm:mt-6 space-y-4">
                        {/* Remarks field for complete and return actions */}
                        {(action === "complete" || action === "return") && (
                            <div>
                                <InputLabel
                                    htmlFor="remarks"
                                    value={
                                        isRemarksRequired
                                            ? "Remarks *"
                                            : "Remarks (Optional)"
                                    }
                                />
                                <textarea
                                    id="remarks"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm sm:text-base"
                                    rows="4"
                                    value={data.remarks}
                                    onChange={(e) =>
                                        setData("remarks", e.target.value)
                                    }
                                    placeholder={
                                        action === "return"
                                            ? "Please explain why you are returning this concept paper..."
                                            : "Add any comments or notes about your review..."
                                    }
                                    required={isRemarksRequired}
                                />
                                <InputError
                                    message={errors.remarks}
                                    className="mt-2"
                                />
                            </div>
                        )}

                        {/* File upload for attachment action */}
                        {action === "attachment" && (
                            <div>
                                <FileUpload
                                    onUpload={handleFileUpload}
                                    accept=".pdf"
                                    maxSize={10}
                                    label="Select PDF File *"
                                    helpText="Upload a supporting document for this stage"
                                    error={errors.attachment}
                                />
                            </div>
                        )}
                    </div>

                    <div className="mt-4 sm:mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-2 sm:gap-3">
                        <SecondaryButton
                            type="button"
                            onClick={onClose}
                            disabled={processing}
                            className="w-full sm:w-auto justify-center"
                        >
                            Cancel
                        </SecondaryButton>

                        <PrimaryButton
                            type="submit"
                            disabled={processing}
                            className={`w-full sm:w-auto justify-center ${
                                action === "return"
                                    ? "bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:ring-orange-500"
                                    : action === "complete"
                                    ? "bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500"
                                    : ""
                            }`}
                        >
                            {processing
                                ? "Processing..."
                                : action === "complete"
                                ? "Complete Stage"
                                : action === "return"
                                ? "Return to Previous"
                                : "Upload Attachment"}
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </Modal>
    );
}

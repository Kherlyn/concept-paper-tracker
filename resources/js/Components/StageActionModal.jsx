import Modal from "@/Components/Modal";
import InputLabel from "@/Components/InputLabel";
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import FileUpload from "@/Components/FileUpload";
import { useForm } from "@inertiajs/react";
import { useEffect, useRef, useState } from "react";

export default function StageActionModal({
    isOpen,
    onClose,
    stage,
    action, // 'complete', 'return', 'reject', or 'attachment'
    conceptPaperId,
}) {
    const { data, setData, post, processing, errors, reset, clearErrors } =
        useForm({
            remarks: "",
            attachment: null,
            signature: "",
            rejection_reason: "",
        });

    const canvasRef = useRef(null);
    const [isDrawing, setIsDrawing] = useState(false);
    const [hasSignature, setHasSignature] = useState(false);

    useEffect(() => {
        if (!isOpen) {
            reset();
            clearErrors();
            setHasSignature(false);
            if (canvasRef.current) {
                const ctx = canvasRef.current.getContext("2d");
                ctx.clearRect(
                    0,
                    0,
                    canvasRef.current.width,
                    canvasRef.current.height
                );
            }
        }
    }, [isOpen]);

    // Initialize canvas
    useEffect(() => {
        if (action === "complete" && canvasRef.current) {
            const canvas = canvasRef.current;
            const ctx = canvas.getContext("2d");
            ctx.strokeStyle = "#000";
            ctx.lineWidth = 2;
            ctx.lineCap = "round";
        }
    }, [action, isOpen]);

    const startDrawing = (e) => {
        setIsDrawing(true);
        const canvas = canvasRef.current;
        const ctx = canvas.getContext("2d");
        const rect = canvas.getBoundingClientRect();
        ctx.beginPath();
        ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
    };

    const draw = (e) => {
        if (!isDrawing) return;
        const canvas = canvasRef.current;
        const ctx = canvas.getContext("2d");
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
        ctx.stroke();
        setHasSignature(true);
    };

    const stopDrawing = () => {
        setIsDrawing(false);
        if (hasSignature && canvasRef.current) {
            const signatureData = canvasRef.current.toDataURL();
            setData("signature", signatureData);
        }
    };

    const clearSignature = () => {
        if (canvasRef.current) {
            const ctx = canvasRef.current.getContext("2d");
            ctx.clearRect(
                0,
                0,
                canvasRef.current.width,
                canvasRef.current.height
            );
            setHasSignature(false);
            setData("signature", "");
        }
    };

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
        } else if (action === "reject") {
            post(route("workflow-stages.reject", stage.id), {
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
                return "Approve Stage with Signature";
            case "return":
                return "Return to Previous Stage";
            case "reject":
                return "Reject Concept Paper";
            case "attachment":
                return "Add Attachment";
            default:
                return "Stage Action";
        }
    };

    const getModalDescription = () => {
        switch (action) {
            case "complete":
                return `You are about to approve the "${stage?.stage_name}" stage. Please provide your digital signature to confirm approval.`;
            case "return":
                return `You are about to return the concept paper to the previous stage. Please provide a reason for returning.`;
            case "reject":
                return `You are about to reject this concept paper. This will stop the workflow. Please provide a detailed reason for rejection.`;
            case "attachment":
                return `Upload a supporting document for the "${stage?.stage_name}" stage. Only PDF and Word documents up to 10MB are allowed.`;
            default:
                return "";
        }
    };

    const isRemarksRequired = action === "return";
    const isRejectionReasonRequired = action === "reject";

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
                        {/* Signature pad for complete action */}
                        {action === "complete" && (
                            <div>
                                <InputLabel
                                    htmlFor="signature"
                                    value="Digital Signature *"
                                />
                                <div className="mt-2 border-2 border-gray-300 rounded-md bg-white">
                                    <canvas
                                        ref={canvasRef}
                                        width={500}
                                        height={150}
                                        className="w-full cursor-crosshair touch-none"
                                        onMouseDown={startDrawing}
                                        onMouseMove={draw}
                                        onMouseUp={stopDrawing}
                                        onMouseLeave={stopDrawing}
                                        onTouchStart={(e) => {
                                            e.preventDefault();
                                            const touch = e.touches[0];
                                            const mouseEvent = new MouseEvent(
                                                "mousedown",
                                                {
                                                    clientX: touch.clientX,
                                                    clientY: touch.clientY,
                                                }
                                            );
                                            canvasRef.current.dispatchEvent(
                                                mouseEvent
                                            );
                                        }}
                                        onTouchMove={(e) => {
                                            e.preventDefault();
                                            const touch = e.touches[0];
                                            const mouseEvent = new MouseEvent(
                                                "mousemove",
                                                {
                                                    clientX: touch.clientX,
                                                    clientY: touch.clientY,
                                                }
                                            );
                                            canvasRef.current.dispatchEvent(
                                                mouseEvent
                                            );
                                        }}
                                        onTouchEnd={(e) => {
                                            e.preventDefault();
                                            const mouseEvent = new MouseEvent(
                                                "mouseup",
                                                {}
                                            );
                                            canvasRef.current.dispatchEvent(
                                                mouseEvent
                                            );
                                        }}
                                    />
                                </div>
                                <div className="mt-2 flex justify-between items-center">
                                    <p className="text-xs text-gray-500">
                                        Sign above to approve this stage
                                    </p>
                                    <button
                                        type="button"
                                        onClick={clearSignature}
                                        className="text-xs text-indigo-600 hover:text-indigo-800"
                                    >
                                        Clear Signature
                                    </button>
                                </div>
                                <InputError
                                    message={errors.signature}
                                    className="mt-2"
                                />
                            </div>
                        )}

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

                        {/* Rejection reason for reject action */}
                        {action === "reject" && (
                            <div>
                                <InputLabel
                                    htmlFor="rejection_reason"
                                    value="Rejection Reason *"
                                />
                                <textarea
                                    id="rejection_reason"
                                    className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm sm:text-base"
                                    rows="4"
                                    value={data.rejection_reason}
                                    onChange={(e) =>
                                        setData(
                                            "rejection_reason",
                                            e.target.value
                                        )
                                    }
                                    placeholder="Please provide a detailed reason for rejecting this concept paper..."
                                    required={isRejectionReasonRequired}
                                />
                                <InputError
                                    message={errors.rejection_reason}
                                    className="mt-2"
                                />
                            </div>
                        )}

                        {/* File upload for attachment action */}
                        {action === "attachment" && (
                            <div>
                                <FileUpload
                                    onUpload={handleFileUpload}
                                    accept=".pdf,.doc,.docx"
                                    maxSize={10}
                                    label="Select Document *"
                                    helpText="Upload a PDF or Word document (max 10MB)"
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
                            disabled={
                                processing ||
                                (action === "complete" && !hasSignature)
                            }
                            className={`w-full sm:w-auto justify-center ${
                                action === "return"
                                    ? "bg-orange-600 hover:bg-orange-700 focus:bg-orange-700 active:bg-orange-900 focus:ring-orange-500"
                                    : action === "complete"
                                    ? "bg-green-600 hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:ring-green-500"
                                    : action === "reject"
                                    ? "bg-red-600 hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:ring-red-500"
                                    : ""
                            }`}
                        >
                            {processing
                                ? "Processing..."
                                : action === "complete"
                                ? "Approve with Signature"
                                : action === "return"
                                ? "Return to Previous"
                                : action === "reject"
                                ? "Reject Paper"
                                : "Upload Attachment"}
                        </PrimaryButton>
                    </div>
                </div>
            </form>
        </Modal>
    );
}

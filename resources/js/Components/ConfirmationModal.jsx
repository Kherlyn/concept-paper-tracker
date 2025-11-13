import Modal from "@/Components/Modal";
import DangerButton from "@/Components/DangerButton";
import SecondaryButton from "@/Components/SecondaryButton";
import PrimaryButton from "@/Components/PrimaryButton";

export default function ConfirmationModal({
    isOpen,
    onConfirm,
    onCancel,
    title = "Confirm Action",
    message = "Are you sure you want to proceed?",
    confirmText = "Confirm",
    cancelText = "Cancel",
    variant = "primary", // 'primary', 'danger'
    processing = false,
}) {
    const ConfirmButtonComponent =
        variant === "danger" ? DangerButton : PrimaryButton;

    return (
        <Modal show={isOpen} onClose={onCancel} maxWidth="md">
            <div
                className="p-6"
                role="alertdialog"
                aria-labelledby="confirmation-title"
                aria-describedby="confirmation-message"
            >
                <h2
                    id="confirmation-title"
                    className="text-lg font-medium text-gray-900"
                >
                    {title}
                </h2>

                <p
                    id="confirmation-message"
                    className="mt-3 text-sm text-gray-600"
                >
                    {message}
                </p>

                <div className="mt-6 flex flex-col-reverse sm:flex-row sm:justify-end gap-3">
                    <SecondaryButton onClick={onCancel} disabled={processing}>
                        {cancelText}
                    </SecondaryButton>

                    <ConfirmButtonComponent
                        onClick={onConfirm}
                        disabled={processing}
                        aria-live="polite"
                    >
                        {processing ? "Processing..." : confirmText}
                    </ConfirmButtonComponent>
                </div>
            </div>
        </Modal>
    );
}

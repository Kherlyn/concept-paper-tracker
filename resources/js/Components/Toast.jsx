import { useEffect, useState } from "react";
import { usePage } from "@inertiajs/react";

export default function Toast() {
    const { flash } = usePage().props;
    const [visible, setVisible] = useState(false);
    const [message, setMessage] = useState("");
    const [type, setType] = useState("success");

    useEffect(() => {
        if (flash?.success) {
            setMessage(flash.success);
            setType("success");
            setVisible(true);
        } else if (flash?.error) {
            setMessage(flash.error);
            setType("error");
            setVisible(true);
        } else if (flash?.warning) {
            setMessage(flash.warning);
            setType("warning");
            setVisible(true);
        } else if (flash?.info) {
            setMessage(flash.info);
            setType("info");
            setVisible(true);
        }
    }, [flash]);

    useEffect(() => {
        if (visible) {
            const timer = setTimeout(() => {
                setVisible(false);
            }, 5000);

            return () => clearTimeout(timer);
        }
    }, [visible]);

    if (!visible) return null;

    const styles = {
        success: {
            bg: "bg-green-50",
            border: "border-green-400",
            text: "text-green-800",
            icon: "text-green-400",
            iconPath: (
                <path
                    fillRule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                    clipRule="evenodd"
                />
            ),
        },
        error: {
            bg: "bg-red-50",
            border: "border-red-400",
            text: "text-red-800",
            icon: "text-red-400",
            iconPath: (
                <path
                    fillRule="evenodd"
                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                    clipRule="evenodd"
                />
            ),
        },
        warning: {
            bg: "bg-yellow-50",
            border: "border-yellow-400",
            text: "text-yellow-800",
            icon: "text-yellow-400",
            iconPath: (
                <path
                    fillRule="evenodd"
                    d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                    clipRule="evenodd"
                />
            ),
        },
        info: {
            bg: "bg-blue-50",
            border: "border-blue-400",
            text: "text-blue-800",
            icon: "text-blue-400",
            iconPath: (
                <path
                    fillRule="evenodd"
                    d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                    clipRule="evenodd"
                />
            ),
        },
    };

    const style = styles[type];

    return (
        <div
            className="fixed top-4 right-4 z-50 animate-slide-in-right"
            role="alert"
            aria-live="assertive"
        >
            <div
                className={`max-w-md rounded-lg border-l-4 ${style.border} ${style.bg} p-4 shadow-lg`}
            >
                <div className="flex items-start">
                    <div className="flex-shrink-0">
                        <svg
                            className={`h-5 w-5 ${style.icon}`}
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            {style.iconPath}
                        </svg>
                    </div>
                    <div className="ml-3 flex-1">
                        <p className={`text-sm font-medium ${style.text}`}>
                            {message}
                        </p>
                    </div>
                    <div className="ml-4 flex flex-shrink-0">
                        <button
                            type="button"
                            onClick={() => setVisible(false)}
                            className={`inline-flex rounded-md ${style.bg} ${style.text} hover:${style.text} focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-${type}-50`}
                        >
                            <span className="sr-only">Close</span>
                            <svg
                                className="h-5 w-5"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                aria-hidden="true"
                            >
                                <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    );
}

import { useState, useRef } from "react";
import InputError from "@/Components/InputError";

export default function FileUpload({
    onUpload,
    accept = ".pdf",
    maxSize = 10,
    error = null,
    label = "Upload File",
    helpText = null,
}) {
    const [isDragging, setIsDragging] = useState(false);
    const [selectedFile, setSelectedFile] = useState(null);
    const fileInputRef = useRef(null);

    const handleDragEnter = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(true);
    };

    const handleDragLeave = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);
    };

    const handleDragOver = (e) => {
        e.preventDefault();
        e.stopPropagation();
    };

    const handleDrop = (e) => {
        e.preventDefault();
        e.stopPropagation();
        setIsDragging(false);

        const files = e.dataTransfer.files;
        if (files && files.length > 0) {
            handleFileSelection(files[0]);
        }
    };

    const handleFileSelection = (file) => {
        // Validate file type
        const acceptedTypes = accept.split(",").map((type) => type.trim());
        const fileExtension = "." + file.name.split(".").pop().toLowerCase();

        if (!acceptedTypes.includes(fileExtension)) {
            alert(`Please select a valid file type: ${accept}`);
            return;
        }

        // Validate file size (maxSize is in MB)
        const maxSizeBytes = maxSize * 1024 * 1024;
        if (file.size > maxSizeBytes) {
            alert(`File size must be less than ${maxSize}MB`);
            return;
        }

        setSelectedFile(file);
        if (onUpload) {
            onUpload(file);
        }
    };

    const handleFileInputChange = (e) => {
        const files = e.target.files;
        if (files && files.length > 0) {
            handleFileSelection(files[0]);
        }
    };

    const handleClick = () => {
        fileInputRef.current?.click();
    };

    const handleRemove = () => {
        setSelectedFile(null);
        if (fileInputRef.current) {
            fileInputRef.current.value = "";
        }
        if (onUpload) {
            onUpload(null);
        }
    };

    const formatFileSize = (bytes) => {
        if (bytes === 0) return "0 Bytes";
        const k = 1024;
        const sizes = ["Bytes", "KB", "MB", "GB"];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return (
            Math.round((bytes / Math.pow(k, i)) * 100) / 100 + " " + sizes[i]
        );
    };

    return (
        <div>
            {label && (
                <label
                    htmlFor="file-upload-input"
                    className="block text-sm font-medium text-gray-700 mb-2"
                >
                    {label}
                </label>
            )}

            <div
                className={`relative border-2 border-dashed rounded-lg p-6 transition-colors ${
                    isDragging
                        ? "border-indigo-500 bg-indigo-50"
                        : error
                        ? "border-red-300 bg-red-50"
                        : "border-gray-300 bg-white hover:border-gray-400"
                }`}
                onDragEnter={handleDragEnter}
                onDragOver={handleDragOver}
                onDragLeave={handleDragLeave}
                onDrop={handleDrop}
                role="region"
                aria-label="File upload area"
            >
                <input
                    ref={fileInputRef}
                    id="file-upload-input"
                    type="file"
                    accept={accept}
                    onChange={handleFileInputChange}
                    className="sr-only"
                    aria-describedby={
                        helpText ? "file-upload-description" : undefined
                    }
                />

                {!selectedFile ? (
                    <div className="text-center">
                        <svg
                            className="mx-auto h-12 w-12 text-gray-400"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 48 48"
                            aria-hidden="true"
                        >
                            <path
                                d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                strokeWidth={2}
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            />
                        </svg>
                        <div className="mt-4">
                            <button
                                type="button"
                                onClick={handleClick}
                                className="text-indigo-600 hover:text-indigo-500 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded px-2 py-1"
                                aria-label="Choose file to upload"
                            >
                                Click to upload
                            </button>
                            <span className="text-gray-500">
                                {" "}
                                or drag and drop
                            </span>
                        </div>
                        <p
                            className="text-xs text-gray-500 mt-2"
                            id="file-upload-description"
                        >
                            {accept.toUpperCase()} up to {maxSize}MB
                        </p>
                        {helpText && (
                            <p className="text-xs text-gray-500 mt-1">
                                {helpText}
                            </p>
                        )}
                    </div>
                ) : (
                    <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div className="flex items-center space-x-3 min-w-0">
                            <svg
                                className="h-10 w-10 text-gray-400 flex-shrink-0"
                                fill="currentColor"
                                viewBox="0 0 20 20"
                                aria-hidden="true"
                            >
                                <path
                                    fillRule="evenodd"
                                    d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                    clipRule="evenodd"
                                />
                            </svg>
                            <div className="min-w-0 flex-1">
                                <p className="text-sm font-medium text-gray-900 truncate">
                                    {selectedFile.name}
                                </p>
                                <p className="text-xs text-gray-500">
                                    {formatFileSize(selectedFile.size)}
                                </p>
                            </div>
                        </div>
                        <button
                            type="button"
                            onClick={handleRemove}
                            className="text-red-600 hover:text-red-800 font-medium text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 rounded px-3 py-1 flex-shrink-0"
                            aria-label={`Remove file ${selectedFile.name}`}
                        >
                            Remove
                        </button>
                    </div>
                )}
            </div>

            {error && <InputError message={error} className="mt-2" />}
        </div>
    );
}

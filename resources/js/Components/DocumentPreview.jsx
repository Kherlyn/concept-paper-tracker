import { useState, useEffect, useRef } from "react";
import Modal from "@/Components/Modal";
import * as pdfjsLib from "pdfjs-dist";
import pdfjsWorker from "pdfjs-dist/build/pdf.worker.min.mjs?url";

// Configure PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

export default function DocumentPreview({
    show = false,
    onClose = () => {},
    attachmentId = null,
    fileName = "Document",
}) {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [pdfDocument, setPdfDocument] = useState(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(0);
    const [scale, setScale] = useState(1.0);
    const [rendering, setRendering] = useState(false);
    const [isPanning, setIsPanning] = useState(false);
    const [panStart, setPanStart] = useState({ x: 0, y: 0 });
    const [panOffset, setPanOffset] = useState({ x: 0, y: 0 });

    const canvasRef = useRef(null);
    const containerRef = useRef(null);

    // Load PDF when modal opens
    useEffect(() => {
        if (show && attachmentId) {
            loadDocument();
        }

        // Cleanup when modal closes
        return () => {
            if (pdfDocument) {
                pdfDocument.destroy();
            }
        };
    }, [show, attachmentId]);

    // Render page when page number or scale changes
    useEffect(() => {
        if (pdfDocument && currentPage > 0) {
            renderPage(currentPage);
        }
    }, [pdfDocument, currentPage, scale]);

    const loadDocument = async () => {
        setLoading(true);
        setError(null);
        setPdfDocument(null);
        setCurrentPage(1);
        setTotalPages(0);

        try {
            const previewUrl = route("attachments.preview", attachmentId);
            const response = await fetch(previewUrl, {
                headers: {
                    Accept: "application/pdf",
                },
            });

            if (!response.ok) {
                // Try to parse JSON error response
                const contentType = response.headers.get("content-type");
                if (contentType && contentType.includes("application/json")) {
                    const errorData = await response.json();
                    throw new Error(
                        errorData.error ||
                            "Preview unavailable. Please download the file instead."
                    );
                }
                throw new Error("Failed to load document preview");
            }

            const arrayBuffer = await response.arrayBuffer();
            const loadingTask = pdfjsLib.getDocument({ data: arrayBuffer });
            const pdf = await loadingTask.promise;

            setPdfDocument(pdf);
            setTotalPages(pdf.numPages);
            setCurrentPage(1);
        } catch (err) {
            console.error("Error loading document:", err);
            setError(
                err.message ||
                    "Failed to load document preview. Please try downloading the file instead."
            );
        } finally {
            setLoading(false);
        }
    };

    const renderPage = async (pageNumber) => {
        if (!pdfDocument || !canvasRef.current || rendering) {
            return;
        }

        setRendering(true);

        try {
            const page = await pdfDocument.getPage(pageNumber);
            const viewport = page.getViewport({ scale });

            const canvas = canvasRef.current;
            const context = canvas.getContext("2d");

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: context,
                viewport: viewport,
            };

            await page.render(renderContext).promise;
        } catch (err) {
            console.error("Error rendering page:", err);
            setError("Failed to render page");
        } finally {
            setRendering(false);
        }
    };

    const handlePreviousPage = () => {
        if (currentPage > 1) {
            setCurrentPage(currentPage - 1);
        }
    };

    const handleNextPage = () => {
        if (currentPage < totalPages) {
            setCurrentPage(currentPage + 1);
        }
    };

    const handleZoomIn = () => {
        setScale((prevScale) => Math.min(prevScale + 0.25, 3.0));
    };

    const handleZoomOut = () => {
        setScale((prevScale) => Math.max(prevScale - 0.25, 0.5));
    };

    const handleResetZoom = () => {
        setScale(1.0);
    };

    const handleDownload = () => {
        window.location.href = route("attachments.download", attachmentId);
    };

    const handleClose = () => {
        // Reset state when closing
        setError(null);
        setLoading(false);
        setPdfDocument(null);
        setCurrentPage(1);
        setTotalPages(0);
        setScale(1.0);
        setPanOffset({ x: 0, y: 0 });
        setIsPanning(false);
        onClose();
    };

    const handleMouseDown = (e) => {
        if (scale > 1.0) {
            setIsPanning(true);
            setPanStart({
                x: e.clientX - panOffset.x,
                y: e.clientY - panOffset.y,
            });
        }
    };

    const handleMouseMove = (e) => {
        if (isPanning) {
            setPanOffset({
                x: e.clientX - panStart.x,
                y: e.clientY - panStart.y,
            });
        }
    };

    const handleMouseUp = () => {
        setIsPanning(false);
    };

    const handleWheel = (e) => {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            const delta = e.deltaY > 0 ? -0.1 : 0.1;
            setScale((prevScale) =>
                Math.max(0.5, Math.min(3.0, prevScale + delta))
            );
        }
    };

    return (
        <Modal show={show} onClose={handleClose} maxWidth="full">
            <div className="flex flex-col h-[90vh]">
                {/* Header */}
                <div className="flex items-center justify-between p-4 border-b border-gray-200 bg-gray-50">
                    <div className="flex items-center space-x-3 min-w-0 flex-1">
                        <svg
                            className="h-6 w-6 text-gray-400 flex-shrink-0"
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
                        <h3 className="text-lg font-semibold text-gray-900 truncate">
                            {fileName}
                        </h3>
                    </div>
                    <button
                        onClick={handleClose}
                        className="ml-3 text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded p-1"
                        aria-label="Close preview"
                    >
                        <svg
                            className="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>

                {/* Toolbar */}
                {!loading && !error && pdfDocument && (
                    <div className="flex items-center justify-between p-3 border-b border-gray-200 bg-white">
                        {/* Page Navigation */}
                        <div className="flex items-center space-x-2">
                            <button
                                onClick={handlePreviousPage}
                                disabled={currentPage <= 1}
                                className="p-2 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="Previous page"
                            >
                                <svg
                                    className="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M15 19l-7-7 7-7"
                                    />
                                </svg>
                            </button>
                            <span className="text-sm text-gray-700 min-w-[100px] text-center">
                                Page {currentPage} of {totalPages}
                            </span>
                            <button
                                onClick={handleNextPage}
                                disabled={currentPage >= totalPages}
                                className="p-2 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="Next page"
                            >
                                <svg
                                    className="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M9 5l7 7-7 7"
                                    />
                                </svg>
                            </button>
                        </div>

                        {/* Zoom Controls */}
                        <div className="flex items-center space-x-2">
                            <button
                                onClick={handleZoomOut}
                                disabled={scale <= 0.5}
                                className="p-2 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="Zoom out"
                            >
                                <svg
                                    className="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"
                                    />
                                </svg>
                            </button>
                            <span className="text-sm text-gray-700 min-w-[60px] text-center">
                                {Math.round(scale * 100)}%
                            </span>
                            <button
                                onClick={handleZoomIn}
                                disabled={scale >= 3.0}
                                className="p-2 rounded hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="Zoom in"
                            >
                                <svg
                                    className="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"
                                    />
                                </svg>
                            </button>
                            <button
                                onClick={handleResetZoom}
                                className="px-3 py-1 text-sm rounded hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="Reset zoom"
                            >
                                Reset
                            </button>
                        </div>

                        {/* Download Button */}
                        <button
                            onClick={handleDownload}
                            className="px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Download
                        </button>
                    </div>
                )}

                {/* Content Area */}
                <div
                    ref={containerRef}
                    className="flex-1 overflow-auto bg-gray-100 p-4"
                    onMouseDown={handleMouseDown}
                    onMouseMove={handleMouseMove}
                    onMouseUp={handleMouseUp}
                    onMouseLeave={handleMouseUp}
                    onWheel={handleWheel}
                    style={{
                        cursor: isPanning
                            ? "grabbing"
                            : scale > 1.0
                            ? "grab"
                            : "default",
                    }}
                >
                    {loading && (
                        <div className="flex flex-col items-center justify-center h-full">
                            <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mb-4"></div>
                            <p className="text-gray-600">
                                Loading document preview...
                            </p>
                        </div>
                    )}

                    {error && (
                        <div className="flex flex-col items-center justify-center h-full">
                            <div className="bg-red-50 border border-red-200 rounded-lg p-6 max-w-md">
                                <div className="flex items-start">
                                    <svg
                                        className="h-6 w-6 text-red-400 flex-shrink-0"
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
                                    <div className="ml-3">
                                        <h3 className="text-sm font-medium text-red-800">
                                            Preview Error
                                        </h3>
                                        <p className="mt-2 text-sm text-red-700">
                                            {error}
                                        </p>
                                        <button
                                            onClick={handleDownload}
                                            className="mt-4 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2"
                                        >
                                            Download File Instead
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    )}

                    {!loading && !error && pdfDocument && (
                        <div className="flex justify-center">
                            <div
                                className="bg-white shadow-lg"
                                style={{
                                    transform: `translate(${panOffset.x}px, ${panOffset.y}px)`,
                                    transition: isPanning
                                        ? "none"
                                        : "transform 0.1s ease-out",
                                }}
                            >
                                <canvas
                                    ref={canvasRef}
                                    className="max-w-full h-auto"
                                />
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </Modal>
    );
}

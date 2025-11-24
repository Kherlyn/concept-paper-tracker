import { useState, useEffect, useRef } from "react";
import Modal from "@/Components/Modal";
import AnnotationCanvas from "@/Components/AnnotationCanvas";
import * as pdfjsLib from "pdfjs-dist";
import pdfjsWorker from "pdfjs-dist/build/pdf.worker.min.mjs?url";
import axios from "axios";

// Configure PDF.js worker
pdfjsLib.GlobalWorkerOptions.workerSrc = pdfjsWorker;

/**
 * DocumentPreviewWithAnnotations Component
 *
 * Enhanced document preview that includes annotation capabilities.
 * Combines PDF rendering with the AnnotationCanvas component.
 */
export default function DocumentPreviewWithAnnotations({
    show = false,
    onClose = () => {},
    attachmentId = null,
    conceptPaperId = null,
    fileName = "Document",
    readOnly = false,
}) {
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState(null);
    const [pdfDocument, setPdfDocument] = useState(null);
    const [currentPage, setCurrentPage] = useState(1);
    const [totalPages, setTotalPages] = useState(0);
    const [pageImages, setPageImages] = useState({});
    const [annotations, setAnnotations] = useState([]);
    const [loadingAnnotations, setLoadingAnnotations] = useState(false);

    // Load PDF and annotations when modal opens
    useEffect(() => {
        if (show && attachmentId) {
            loadDocument();
            loadAnnotations();
        }

        // Cleanup when modal closes
        return () => {
            if (pdfDocument) {
                pdfDocument.destroy();
            }
        };
    }, [show, attachmentId]);

    // Render current page as image for annotation canvas
    useEffect(() => {
        if (pdfDocument && currentPage > 0 && !pageImages[currentPage]) {
            renderPageToImage(currentPage);
        }
    }, [pdfDocument, currentPage]);

    const loadDocument = async () => {
        setLoading(true);
        setError(null);
        setPdfDocument(null);
        setCurrentPage(1);
        setTotalPages(0);
        setPageImages({});

        try {
            const previewUrl = route("attachments.preview", attachmentId);
            const response = await fetch(previewUrl, {
                headers: {
                    Accept: "application/pdf",
                },
            });

            if (!response.ok) {
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

    const loadAnnotations = async () => {
        if (!attachmentId) return;

        setLoadingAnnotations(true);
        try {
            const response = await axios.get("/annotations", {
                params: {
                    attachment_id: attachmentId,
                },
            });

            if (response.data.success) {
                setAnnotations(response.data.data);
            }
        } catch (err) {
            console.error("Error loading annotations:", err);
        } finally {
            setLoadingAnnotations(false);
        }
    };

    const renderPageToImage = async (pageNumber) => {
        if (!pdfDocument) return;

        try {
            const page = await pdfDocument.getPage(pageNumber);
            const viewport = page.getViewport({ scale: 1.5 });

            const canvas = document.createElement("canvas");
            const context = canvas.getContext("2d");

            canvas.height = viewport.height;
            canvas.width = viewport.width;

            const renderContext = {
                canvasContext: context,
                viewport: viewport,
            };

            await page.render(renderContext).promise;

            // Convert canvas to data URL
            const imageUrl = canvas.toDataURL();

            setPageImages((prev) => ({
                ...prev,
                [pageNumber]: {
                    url: imageUrl,
                    width: viewport.width,
                    height: viewport.height,
                },
            }));
        } catch (err) {
            console.error("Error rendering page to image:", err);
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

    const handleDownload = () => {
        window.location.href = route("attachments.download", attachmentId);
    };

    const handleClose = () => {
        setError(null);
        setLoading(false);
        setPdfDocument(null);
        setCurrentPage(1);
        setTotalPages(0);
        setPageImages({});
        setAnnotations([]);
        onClose();
    };

    const handleAnnotationSaved = (newAnnotation) => {
        setAnnotations((prev) => [...prev, newAnnotation]);
    };

    const handleAnnotationDeleted = (annotationId) => {
        setAnnotations((prev) => prev.filter((a) => a.id !== annotationId));
    };

    const currentPageAnnotations = annotations.filter(
        (a) => a.page_number === currentPage
    );

    const currentPageImage = pageImages[currentPage];

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
                        {readOnly && (
                            <span className="px-2 py-1 text-xs font-medium bg-gray-200 text-gray-700 rounded">
                                Read Only
                            </span>
                        )}
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
                <div className="flex-1 overflow-auto bg-gray-100 p-4">
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

                    {!loading && !error && pdfDocument && currentPageImage && (
                        <div className="flex justify-center">
                            <div className="bg-white shadow-lg p-4">
                                <AnnotationCanvas
                                    imageUrl={currentPageImage.url}
                                    pageNumber={currentPage}
                                    conceptPaperId={conceptPaperId}
                                    attachmentId={attachmentId}
                                    canvasWidth={currentPageImage.width}
                                    canvasHeight={currentPageImage.height}
                                    existingAnnotations={currentPageAnnotations}
                                    onAnnotationSaved={handleAnnotationSaved}
                                    onAnnotationDeleted={
                                        handleAnnotationDeleted
                                    }
                                    readOnly={readOnly}
                                />
                            </div>
                        </div>
                    )}

                    {!loading && !error && pdfDocument && !currentPageImage && (
                        <div className="flex items-center justify-center h-full">
                            <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
                            <span className="ml-3 text-gray-600">
                                Rendering page...
                            </span>
                        </div>
                    )}
                </div>
            </div>
        </Modal>
    );
}

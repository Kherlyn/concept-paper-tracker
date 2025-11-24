import { useEffect, useRef, useState } from "react";
import { Canvas, Rect, Path } from "fabric";
import axios from "axios";

/**
 * AnnotationCanvas Component
 *
 * Provides canvas-based annotation tools for document previews including:
 * - Marker tool for highlighting areas
 * - Highlight tool for text highlighting
 * - Drawing tool for freehand annotations
 * - Discrepancy markers with required comments
 *
 * Supports touch gestures for mobile devices and saves annotations to the backend.
 */
export default function AnnotationCanvas({
    imageUrl = null,
    pageNumber = 1,
    conceptPaperId = null,
    attachmentId = null,
    canvasWidth = 800,
    canvasHeight = 1000,
    onAnnotationSaved = () => {},
    onAnnotationDeleted = () => {},
    existingAnnotations = [],
    readOnly = false,
}) {
    const canvasRef = useRef(null);
    const fabricCanvasRef = useRef(null);
    const [activeTool, setActiveTool] = useState(null);
    const [isDrawing, setIsDrawing] = useState(false);
    const [showCommentModal, setShowCommentModal] = useState(false);
    const [pendingAnnotation, setPendingAnnotation] = useState(null);
    const [comment, setComment] = useState("");
    const [isDiscrepancy, setIsDiscrepancy] = useState(false);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState(null);

    // Initialize Fabric.js canvas
    useEffect(() => {
        if (!canvasRef.current) return;

        const canvas = new Canvas(canvasRef.current, {
            width: canvasWidth,
            height: canvasHeight,
            isDrawingMode: false,
            selection: !readOnly,
        });

        // Enable touch support
        canvas.allowTouchScrolling = true;
        canvas.enablePointerEvents = true;

        fabricCanvasRef.current = canvas;

        // Load existing annotations
        loadAnnotations(canvas, existingAnnotations);

        // Cleanup on unmount
        return () => {
            canvas.dispose();
        };
    }, [canvasWidth, canvasHeight, readOnly]);

    // Update when existing annotations change
    useEffect(() => {
        if (fabricCanvasRef.current) {
            loadAnnotations(fabricCanvasRef.current, existingAnnotations);
        }
    }, [existingAnnotations]);

    // Handle tool changes
    useEffect(() => {
        if (!fabricCanvasRef.current) return;

        const canvas = fabricCanvasRef.current;

        if (activeTool === "drawing") {
            canvas.isDrawingMode = true;
            canvas.freeDrawingBrush.color = "#3B82F6";
            canvas.freeDrawingBrush.width = 3;
        } else {
            canvas.isDrawingMode = false;
        }

        // Add event listeners for marker and highlight tools
        if (activeTool === "marker" || activeTool === "highlight") {
            canvas.on("mouse:down", handleMouseDown);
            canvas.on("mouse:move", handleMouseMove);
            canvas.on("mouse:up", handleMouseUp);
        } else {
            canvas.off("mouse:down", handleMouseDown);
            canvas.off("mouse:move", handleMouseMove);
            canvas.off("mouse:up", handleMouseUp);
        }

        // Handle path creation for drawing tool
        if (activeTool === "drawing") {
            canvas.on("path:created", handlePathCreated);
        } else {
            canvas.off("path:created", handlePathCreated);
        }

        return () => {
            canvas.off("mouse:down", handleMouseDown);
            canvas.off("mouse:move", handleMouseMove);
            canvas.off("mouse:up", handleMouseUp);
            canvas.off("path:created", handlePathCreated);
        };
    }, [activeTool, isDrawing]);

    /**
     * Load existing annotations onto the canvas
     */
    const loadAnnotations = (canvas, annotations) => {
        // Clear existing objects
        canvas.clear();

        annotations.forEach((annotation) => {
            const coords = annotation.coordinates;
            let fabricObject = null;

            switch (annotation.annotation_type) {
                case "marker":
                    fabricObject = new Rect({
                        left: coords.x,
                        top: coords.y,
                        width: coords.width,
                        height: coords.height,
                        fill: annotation.is_discrepancy
                            ? "rgba(239, 68, 68, 0.3)"
                            : "rgba(59, 130, 246, 0.3)",
                        stroke: annotation.is_discrepancy
                            ? "#DC2626"
                            : "#3B82F6",
                        strokeWidth: 2,
                        selectable: !readOnly,
                    });
                    break;

                case "highlight":
                    fabricObject = new Rect({
                        left: coords.x,
                        top: coords.y,
                        width: coords.width,
                        height: coords.height,
                        fill: annotation.is_discrepancy
                            ? "rgba(239, 68, 68, 0.2)"
                            : "rgba(250, 204, 21, 0.4)",
                        stroke: "transparent",
                        selectable: !readOnly,
                    });
                    break;

                case "drawing":
                    if (coords.points && Array.isArray(coords.points)) {
                        fabricObject = new Path(
                            pointsToSVGPath(coords.points),
                            {
                                stroke: annotation.is_discrepancy
                                    ? "#DC2626"
                                    : "#3B82F6",
                                strokeWidth: 3,
                                fill: "",
                                selectable: !readOnly,
                            }
                        );
                    }
                    break;
            }

            if (fabricObject) {
                fabricObject.annotationId = annotation.id;
                fabricObject.annotationData = annotation;
                canvas.add(fabricObject);
            }
        });

        canvas.renderAll();
    };

    /**
     * Convert points array to SVG path string
     */
    const pointsToSVGPath = (points) => {
        if (!points || points.length === 0) return "";

        let path = `M ${points[0][0]} ${points[0][1]}`;
        for (let i = 1; i < points.length; i++) {
            path += ` L ${points[i][0]} ${points[i][1]}`;
        }
        return path;
    };

    /**
     * Handle mouse down for marker/highlight tools
     */
    const handleMouseDown = (event) => {
        if (!activeTool || readOnly) return;

        const pointer = fabricCanvasRef.current.getPointer(event.e);
        setIsDrawing(true);

        const rect = new Rect({
            left: pointer.x,
            top: pointer.y,
            width: 0,
            height: 0,
            fill:
                activeTool === "marker"
                    ? "rgba(59, 130, 246, 0.3)"
                    : "rgba(250, 204, 21, 0.4)",
            stroke: activeTool === "marker" ? "#3B82F6" : "transparent",
            strokeWidth: 2,
            selectable: false,
        });

        rect.startX = pointer.x;
        rect.startY = pointer.y;

        fabricCanvasRef.current.add(rect);
        fabricCanvasRef.current.setActiveObject(rect);
    };

    /**
     * Handle mouse move for marker/highlight tools
     */
    const handleMouseMove = (event) => {
        if (!isDrawing || !activeTool || readOnly) return;

        const pointer = fabricCanvasRef.current.getPointer(event.e);
        const activeObject = fabricCanvasRef.current.getActiveObject();

        if (!activeObject) return;

        const width = pointer.x - activeObject.startX;
        const height = pointer.y - activeObject.startY;

        activeObject.set({
            width: Math.abs(width),
            height: Math.abs(height),
            left: width < 0 ? pointer.x : activeObject.startX,
            top: height < 0 ? pointer.y : activeObject.startY,
        });

        fabricCanvasRef.current.renderAll();
    };

    /**
     * Handle mouse up for marker/highlight tools
     */
    const handleMouseUp = () => {
        if (!isDrawing || !activeTool || readOnly) return;

        setIsDrawing(false);
        const activeObject = fabricCanvasRef.current.getActiveObject();

        if (!activeObject) return;

        // Only save if the annotation has meaningful size
        if (activeObject.width > 5 && activeObject.height > 5) {
            const annotationData = {
                annotation_type: activeTool,
                coordinates: {
                    x: activeObject.left,
                    y: activeObject.top,
                    width: activeObject.width,
                    height: activeObject.height,
                },
            };

            setPendingAnnotation(annotationData);
            setShowCommentModal(true);
        } else {
            // Remove small/accidental annotations
            fabricCanvasRef.current.remove(activeObject);
        }
    };

    /**
     * Handle path creation for drawing tool
     */
    const handlePathCreated = (event) => {
        if (!activeTool || activeTool !== "drawing" || readOnly) return;

        const path = event.path;
        const pathData = path.path;

        // Extract points from path
        const points = pathData
            .filter((segment) => segment[0] === "L" || segment[0] === "M")
            .map((segment) => [segment[1], segment[2]]);

        const annotationData = {
            annotation_type: "drawing",
            coordinates: {
                x: path.left,
                y: path.top,
                points: points,
            },
        };

        setPendingAnnotation(annotationData);
        setShowCommentModal(true);
    };

    /**
     * Save annotation to backend
     */
    const saveAnnotation = async () => {
        if (!pendingAnnotation || !conceptPaperId || !attachmentId) return;

        setSaving(true);
        setError(null);

        try {
            const payload = {
                concept_paper_id: conceptPaperId,
                attachment_id: attachmentId,
                page_number: pageNumber,
                annotation_type: pendingAnnotation.annotation_type,
                coordinates: pendingAnnotation.coordinates,
                comment: comment.trim() || null,
                is_discrepancy: isDiscrepancy,
                max_width: canvasWidth,
                max_height: canvasHeight,
            };

            const response = await axios.post("/api/annotations", payload);

            if (response.data.success) {
                setShowCommentModal(false);
                setComment("");
                setIsDiscrepancy(false);
                setPendingAnnotation(null);
                onAnnotationSaved(response.data.data);
            }
        } catch (err) {
            console.error("Error saving annotation:", err);
            setError(
                err.response?.data?.message ||
                    "Failed to save annotation. Please try again."
            );
        } finally {
            setSaving(false);
        }
    };

    /**
     * Cancel annotation creation
     */
    const cancelAnnotation = () => {
        // Remove the pending annotation from canvas
        const activeObject = fabricCanvasRef.current.getActiveObject();
        if (activeObject) {
            fabricCanvasRef.current.remove(activeObject);
        }

        setShowCommentModal(false);
        setComment("");
        setIsDiscrepancy(false);
        setPendingAnnotation(null);
    };

    /**
     * Delete an annotation
     */
    const deleteAnnotation = async (annotationId) => {
        if (!annotationId || readOnly) return;

        try {
            const response = await axios.delete(
                `/api/annotations/${annotationId}`
            );

            if (response.data.success) {
                // Remove from canvas
                const objects = fabricCanvasRef.current.getObjects();
                const objectToRemove = objects.find(
                    (obj) => obj.annotationId === annotationId
                );

                if (objectToRemove) {
                    fabricCanvasRef.current.remove(objectToRemove);
                }

                onAnnotationDeleted(annotationId);
            }
        } catch (err) {
            console.error("Error deleting annotation:", err);
            setError("Failed to delete annotation. Please try again.");
        }
    };

    /**
     * Handle object selection for deletion
     */
    useEffect(() => {
        if (!fabricCanvasRef.current || readOnly) return;

        const canvas = fabricCanvasRef.current;

        const handleSelection = (event) => {
            const selectedObject = event.selected?.[0];
            if (selectedObject && selectedObject.annotationId) {
                // Show delete option
                console.log(
                    "Selected annotation:",
                    selectedObject.annotationId
                );
            }
        };

        canvas.on("selection:created", handleSelection);
        canvas.on("selection:updated", handleSelection);

        return () => {
            canvas.off("selection:created", handleSelection);
            canvas.off("selection:updated", handleSelection);
        };
    }, [readOnly]);

    /**
     * Handle keyboard delete
     */
    useEffect(() => {
        if (!fabricCanvasRef.current || readOnly) return;

        const handleKeyDown = (e) => {
            if (e.key === "Delete" || e.key === "Backspace") {
                const activeObject = fabricCanvasRef.current.getActiveObject();
                if (activeObject && activeObject.annotationId) {
                    deleteAnnotation(activeObject.annotationId);
                }
            }
        };

        window.addEventListener("keydown", handleKeyDown);

        return () => {
            window.removeEventListener("keydown", handleKeyDown);
        };
    }, [readOnly]);

    return (
        <div className="annotation-canvas-container">
            {/* Toolbar */}
            {!readOnly && (
                <div className="flex items-center space-x-2 mb-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                    <button
                        onClick={() =>
                            setActiveTool(
                                activeTool === "marker" ? null : "marker"
                            )
                        }
                        className={`px-4 py-2 rounded-md text-sm font-medium transition-colors ${
                            activeTool === "marker"
                                ? "bg-blue-600 text-white"
                                : "bg-white text-gray-700 hover:bg-gray-100 border border-gray-300"
                        }`}
                        title="Marker tool - Draw rectangles to mark areas"
                    >
                        <svg
                            className="w-5 h-5 inline-block mr-1"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5z"
                            />
                        </svg>
                        Marker
                    </button>

                    <button
                        onClick={() =>
                            setActiveTool(
                                activeTool === "highlight" ? null : "highlight"
                            )
                        }
                        className={`px-4 py-2 rounded-md text-sm font-medium transition-colors ${
                            activeTool === "highlight"
                                ? "bg-yellow-500 text-white"
                                : "bg-white text-gray-700 hover:bg-gray-100 border border-gray-300"
                        }`}
                        title="Highlight tool - Highlight text areas"
                    >
                        <svg
                            className="w-5 h-5 inline-block mr-1"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                        </svg>
                        Highlight
                    </button>

                    <button
                        onClick={() =>
                            setActiveTool(
                                activeTool === "drawing" ? null : "drawing"
                            )
                        }
                        className={`px-4 py-2 rounded-md text-sm font-medium transition-colors ${
                            activeTool === "drawing"
                                ? "bg-blue-600 text-white"
                                : "bg-white text-gray-700 hover:bg-gray-100 border border-gray-300"
                        }`}
                        title="Drawing tool - Freehand drawing"
                    >
                        <svg
                            className="w-5 h-5 inline-block mr-1"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth={2}
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                            />
                        </svg>
                        Draw
                    </button>

                    <div className="flex-1"></div>

                    {activeTool && (
                        <span className="text-sm text-gray-600">
                            {activeTool === "marker" &&
                                "Click and drag to mark an area"}
                            {activeTool === "highlight" &&
                                "Click and drag to highlight text"}
                            {activeTool === "drawing" &&
                                "Draw freehand on the document"}
                        </span>
                    )}
                </div>
            )}

            {/* Canvas */}
            <div className="border border-gray-300 rounded-lg overflow-hidden bg-white">
                <canvas ref={canvasRef} />
            </div>

            {/* Comment Modal */}
            {showCommentModal && (
                <div className="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div className="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                        <div className="p-6">
                            <h3 className="text-lg font-semibold text-gray-900 mb-4">
                                Add Annotation Details
                            </h3>

                            {/* Discrepancy Checkbox */}
                            <div className="mb-4">
                                <label className="flex items-center space-x-2 cursor-pointer">
                                    <input
                                        type="checkbox"
                                        checked={isDiscrepancy}
                                        onChange={(e) =>
                                            setIsDiscrepancy(e.target.checked)
                                        }
                                        className="rounded border-gray-300 text-red-600 focus:ring-red-500"
                                    />
                                    <span className="text-sm font-medium text-gray-700">
                                        Mark as discrepancy
                                    </span>
                                </label>
                                {isDiscrepancy && (
                                    <p className="mt-1 text-xs text-red-600">
                                        Discrepancies require a comment
                                    </p>
                                )}
                            </div>

                            {/* Comment Input */}
                            <div className="mb-4">
                                <label
                                    htmlFor="annotation-comment"
                                    className="block text-sm font-medium text-gray-700 mb-2"
                                >
                                    Comment {isDiscrepancy && "(Required)"}
                                </label>
                                <textarea
                                    id="annotation-comment"
                                    value={comment}
                                    onChange={(e) => setComment(e.target.value)}
                                    rows={4}
                                    className="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"
                                    placeholder="Add a comment about this annotation..."
                                />
                            </div>

                            {/* Error Message */}
                            {error && (
                                <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                                    <p className="text-sm text-red-700">
                                        {error}
                                    </p>
                                </div>
                            )}

                            {/* Actions */}
                            <div className="flex justify-end space-x-3">
                                <button
                                    onClick={cancelAnnotation}
                                    disabled={saving}
                                    className="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50"
                                >
                                    Cancel
                                </button>
                                <button
                                    onClick={saveAnnotation}
                                    disabled={
                                        saving ||
                                        (isDiscrepancy && !comment.trim())
                                    }
                                    className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    {saving ? "Saving..." : "Save Annotation"}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Instructions for mobile */}
            {!readOnly && (
                <div className="mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                    <p className="text-sm text-blue-800">
                        <strong>Tip:</strong> Select an annotation and press
                        Delete or Backspace to remove it. On mobile, use touch
                        gestures to draw and annotate.
                    </p>
                </div>
            )}
        </div>
    );
}

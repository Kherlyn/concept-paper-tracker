import { Head, Link } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import ReactMarkdown from "react-markdown";
import remarkGfm from "remark-gfm";
import rehypeHighlight from "rehype-highlight";
import WorkflowVisualization from "@/Components/WorkflowVisualization";
import {
    ChevronLeftIcon,
    ChevronRightIcon,
    HomeIcon,
} from "@heroicons/react/24/outline";
import "highlight.js/styles/github-dark.css";

export default function UserGuideSection({ section, content, navigation }) {
    // Check if this is the workflow section to show the visualization
    const isWorkflowSection = section === "workflow";

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <div className="flex items-center space-x-2">
                        <Link
                            href={route("user-guide")}
                            className="text-gray-600 hover:text-gray-900"
                        >
                            <HomeIcon className="h-5 w-5" />
                        </Link>
                        <span className="text-gray-400">/</span>
                        <h2 className="text-xl font-semibold leading-tight text-gray-800">
                            {content.title}
                        </h2>
                    </div>
                    <Link
                        href={route("user-guide")}
                        className="text-sm text-gray-600 hover:text-gray-900"
                    >
                        ← Back to Guide
                    </Link>
                </div>
            }
        >
            <Head title={`User Guide - ${content.title}`} />

            <div className="py-12">
                <div className="mx-auto max-w-4xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-8">
                            {/* Workflow Visualization - shown before markdown content for workflow section */}
                            {isWorkflowSection && (
                                <div className="mb-8">
                                    <h3 className="text-2xl font-bold text-gray-900 mb-6">
                                        Interactive Workflow Diagram
                                    </h3>
                                    <WorkflowVisualization variant="detailed" />
                                </div>
                            )}

                            {/* Content */}
                            <article className="prose prose-indigo max-w-none">
                                <ReactMarkdown
                                    remarkPlugins={[remarkGfm]}
                                    rehypePlugins={[rehypeHighlight]}
                                    components={{
                                        // Custom rendering for code blocks
                                        code({
                                            node,
                                            inline,
                                            className,
                                            children,
                                            ...props
                                        }) {
                                            return inline ? (
                                                <code
                                                    className={className}
                                                    {...props}
                                                >
                                                    {children}
                                                </code>
                                            ) : (
                                                <code
                                                    className={className}
                                                    {...props}
                                                >
                                                    {children}
                                                </code>
                                            );
                                        },
                                        // Custom rendering for tables
                                        table({ children }) {
                                            return (
                                                <div className="overflow-x-auto my-6">
                                                    <table className="min-w-full divide-y divide-gray-300">
                                                        {children}
                                                    </table>
                                                </div>
                                            );
                                        },
                                        // Custom rendering for headings with anchor links
                                        h1({ children }) {
                                            return (
                                                <h1 className="text-3xl font-bold text-gray-900 mt-0 mb-6">
                                                    {children}
                                                </h1>
                                            );
                                        },
                                        h2({ children }) {
                                            return (
                                                <h2 className="text-2xl font-semibold text-gray-900 mt-8 mb-4 pb-2 border-b border-gray-200">
                                                    {children}
                                                </h2>
                                            );
                                        },
                                        h3({ children }) {
                                            return (
                                                <h3 className="text-xl font-semibold text-gray-900 mt-6 mb-3">
                                                    {children}
                                                </h3>
                                            );
                                        },
                                        h4({ children }) {
                                            return (
                                                <h4 className="text-lg font-semibold text-gray-900 mt-5 mb-2">
                                                    {children}
                                                </h4>
                                            );
                                        },
                                        // Custom rendering for lists
                                        ul({ children }) {
                                            return (
                                                <ul className="list-disc list-outside ml-6 my-4 space-y-2">
                                                    {children}
                                                </ul>
                                            );
                                        },
                                        ol({ children }) {
                                            return (
                                                <ol className="list-decimal list-outside ml-6 my-4 space-y-2">
                                                    {children}
                                                </ol>
                                            );
                                        },
                                        // Custom rendering for blockquotes
                                        blockquote({ children }) {
                                            return (
                                                <blockquote className="border-l-4 border-indigo-500 bg-indigo-50 pl-4 pr-4 py-3 my-4 italic">
                                                    {children}
                                                </blockquote>
                                            );
                                        },
                                    }}
                                >
                                    {content.markdown}
                                </ReactMarkdown>
                            </article>

                            {/* Last Updated */}
                            <div className="mt-8 pt-6 border-t border-gray-200">
                                <p className="text-sm text-gray-500">
                                    Last updated: {content.lastUpdated}
                                </p>
                            </div>

                            {/* Navigation */}
                            <div className="mt-8 flex justify-between items-center">
                                {navigation.previous ? (
                                    <Link
                                        href={route(
                                            "user-guide.section",
                                            navigation.previous
                                        )}
                                        className="flex items-center px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-md transition-colors"
                                    >
                                        <ChevronLeftIcon className="h-5 w-5 mr-1" />
                                        Previous
                                    </Link>
                                ) : (
                                    <div />
                                )}

                                {navigation.next ? (
                                    <Link
                                        href={route(
                                            "user-guide.section",
                                            navigation.next
                                        )}
                                        className="flex items-center px-4 py-2 text-sm font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-md transition-colors"
                                    >
                                        Next
                                        <ChevronRightIcon className="h-5 w-5 ml-1" />
                                    </Link>
                                ) : (
                                    <div />
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

import { Head, Link } from "@inertiajs/react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import {
    PlayIcon,
    DocumentTextIcon,
    CheckCircleIcon,
    CogIcon,
    ArrowPathIcon,
    QuestionMarkCircleIcon,
} from "@heroicons/react/24/outline";

const iconMap = {
    play: PlayIcon,
    document: DocumentTextIcon,
    "check-circle": CheckCircleIcon,
    cog: CogIcon,
    flow: ArrowPathIcon,
    question: QuestionMarkCircleIcon,
};

export default function UserGuideIndex({ sections, tableOfContents }) {
    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800">
                    User Guide
                </h2>
            }
        >
            <Head title="User Guide" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Introduction */}
                    <div className="mb-8 overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h1 className="text-3xl font-bold mb-4">
                                Concept Paper Tracker User Guide
                            </h1>
                            <p className="text-lg text-gray-600 mb-4">
                                Welcome to the comprehensive user guide for the
                                Concept Paper Tracker system. This guide will
                                help you understand how to use the system
                                effectively based on your role.
                            </p>
                            <p className="text-gray-600">
                                Select a section below to get started, or use
                                the search function to find specific topics.
                            </p>
                        </div>
                    </div>

                    {/* Table of Contents */}
                    <div className="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                        {tableOfContents.map((section) => {
                            const Icon = iconMap[section.icon];
                            return (
                                <Link
                                    key={section.id}
                                    href={route(
                                        "user-guide.section",
                                        section.id
                                    )}
                                    className="block p-6 bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow"
                                >
                                    <div className="flex items-center mb-4">
                                        <div className="p-2 bg-indigo-100 rounded-lg">
                                            <Icon className="h-6 w-6 text-indigo-600" />
                                        </div>
                                        <h3 className="ml-3 text-lg font-semibold text-gray-900">
                                            {section.title}
                                        </h3>
                                    </div>
                                    <ul className="space-y-2">
                                        {Object.entries(
                                            section.subsections
                                        ).map(([key, title]) => (
                                            <li
                                                key={key}
                                                className="text-sm text-gray-600"
                                            >
                                                • {title}
                                            </li>
                                        ))}
                                    </ul>
                                </Link>
                            );
                        })}
                    </div>

                    {/* Quick Links */}
                    <div className="mt-8 p-6 bg-blue-50 rounded-lg">
                        <h3 className="text-lg font-semibold text-blue-900 mb-4">
                            Quick Links
                        </h3>
                        <div className="grid md:grid-cols-3 gap-4">
                            <Link
                                href={route(
                                    "user-guide.section",
                                    "getting-started"
                                )}
                                className="text-blue-700 hover:text-blue-900 underline"
                            >
                                → Getting Started Guide
                            </Link>
                            <Link
                                href={route("user-guide.section", "workflow")}
                                className="text-blue-700 hover:text-blue-900 underline"
                            >
                                → Workflow Process
                            </Link>
                            <Link
                                href={route("user-guide.section", "faq")}
                                className="text-blue-700 hover:text-blue-900 underline"
                            >
                                → Frequently Asked Questions
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

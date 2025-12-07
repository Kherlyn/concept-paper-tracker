import { useState, Fragment } from "react";
import { useForm, usePage } from "@inertiajs/react";
import { Dialog, Transition } from "@headlessui/react";
import {
    ExclamationTriangleIcon,
    XMarkIcon,
    BugAntIcon,
} from "@heroicons/react/24/outline";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";

export default function ReportErrorButton() {
    const [isOpen, setIsOpen] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({
        title: "",
        description: "",
        steps_to_reproduce: "",
        error_message: "",
        page_url: window.location.href,
        browser_info: navigator.userAgent,
    });

    const openModal = () => setIsOpen(true);
    const closeModal = () => {
        setIsOpen(false);
        reset();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("error-reports.store"), {
            preserveScroll: true,
            onSuccess: () => {
                closeModal();
            },
        });
    };

    return (
        <>
            {/* Floating Report Button */}
            <button
                onClick={openModal}
                className="fixed bottom-6 right-6 z-50 flex items-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200 group"
                title="Report an Error"
            >
                <BugAntIcon className="h-5 w-5" />
                <span className="hidden sm:inline font-medium">
                    Report Issue
                </span>
            </button>

            {/* Report Modal */}
            <Transition appear show={isOpen} as={Fragment}>
                <Dialog as="div" className="relative z-50" onClose={closeModal}>
                    <Transition.Child
                        as={Fragment}
                        enter="ease-out duration-300"
                        enterFrom="opacity-0"
                        enterTo="opacity-100"
                        leave="ease-in duration-200"
                        leaveFrom="opacity-100"
                        leaveTo="opacity-0"
                    >
                        <div className="fixed inset-0 bg-black bg-opacity-25" />
                    </Transition.Child>

                    <div className="fixed inset-0 overflow-y-auto">
                        <div className="flex min-h-full items-center justify-center p-4 text-center">
                            <Transition.Child
                                as={Fragment}
                                enter="ease-out duration-300"
                                enterFrom="opacity-0 scale-95"
                                enterTo="opacity-100 scale-100"
                                leave="ease-in duration-200"
                                leaveFrom="opacity-100 scale-100"
                                leaveTo="opacity-0 scale-95"
                            >
                                <Dialog.Panel className="w-full max-w-lg transform overflow-hidden rounded-2xl bg-white p-6 text-left align-middle shadow-xl transition-all">
                                    <div className="flex items-center justify-between mb-4">
                                        <Dialog.Title
                                            as="h3"
                                            className="text-lg font-semibold leading-6 text-gray-900 flex items-center gap-2"
                                        >
                                            <ExclamationTriangleIcon className="h-6 w-6 text-red-600" />
                                            Report an Error
                                        </Dialog.Title>
                                        <button
                                            onClick={closeModal}
                                            className="text-gray-400 hover:text-gray-500"
                                        >
                                            <XMarkIcon className="h-6 w-6" />
                                        </button>
                                    </div>

                                    <p className="text-sm text-gray-500 mb-4">
                                        Found an issue? Let us know and we'll
                                        fix it as soon as possible. Your
                                        feedback helps improve the system.
                                    </p>

                                    <form
                                        onSubmit={handleSubmit}
                                        className="space-y-4"
                                    >
                                        <div>
                                            <InputLabel
                                                htmlFor="title"
                                                value="Issue Title *"
                                            />
                                            <TextInput
                                                id="title"
                                                type="text"
                                                value={data.title}
                                                className="mt-1 block w-full"
                                                onChange={(e) =>
                                                    setData(
                                                        "title",
                                                        e.target.value
                                                    )
                                                }
                                                placeholder="Brief summary of the issue"
                                                required
                                            />
                                            <InputError
                                                message={errors.title}
                                                className="mt-2"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                htmlFor="description"
                                                value="Description *"
                                            />
                                            <textarea
                                                id="description"
                                                value={data.description}
                                                onChange={(e) =>
                                                    setData(
                                                        "description",
                                                        e.target.value
                                                    )
                                                }
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                rows="3"
                                                placeholder="Describe what went wrong in detail"
                                                required
                                            />
                                            <InputError
                                                message={errors.description}
                                                className="mt-2"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                htmlFor="steps_to_reproduce"
                                                value="Steps to Reproduce (Optional)"
                                            />
                                            <textarea
                                                id="steps_to_reproduce"
                                                value={data.steps_to_reproduce}
                                                onChange={(e) =>
                                                    setData(
                                                        "steps_to_reproduce",
                                                        e.target.value
                                                    )
                                                }
                                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                                rows="2"
                                                placeholder="1. Click on...&#10;2. Then...&#10;3. Error appears..."
                                            />
                                            <InputError
                                                message={
                                                    errors.steps_to_reproduce
                                                }
                                                className="mt-2"
                                            />
                                        </div>

                                        <div>
                                            <InputLabel
                                                htmlFor="error_message"
                                                value="Error Message (Optional)"
                                            />
                                            <TextInput
                                                id="error_message"
                                                type="text"
                                                value={data.error_message}
                                                className="mt-1 block w-full"
                                                onChange={(e) =>
                                                    setData(
                                                        "error_message",
                                                        e.target.value
                                                    )
                                                }
                                                placeholder="Copy any error message you saw"
                                            />
                                            <InputError
                                                message={errors.error_message}
                                                className="mt-2"
                                            />
                                        </div>

                                        <div className="bg-gray-50 rounded-lg p-3 text-xs text-gray-500">
                                            <p className="font-medium mb-1">
                                                Automatically included:
                                            </p>
                                            <p>
                                                • Current page URL: {data.page_url?.substring(0, 50)}...
                                            </p>
                                            <p>
                                                • Browser info will be included
                                            </p>
                                        </div>

                                        <div className="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                                            <button
                                                type="button"
                                                onClick={closeModal}
                                                className="px-4 py-2 text-sm font-medium text-gray-700 hover:text-gray-900"
                                            >
                                                Cancel
                                            </button>
                                            <PrimaryButton
                                                disabled={processing}
                                                className="bg-red-600 hover:bg-red-700 focus:bg-red-700"
                                            >
                                                {processing
                                                    ? "Submitting..."
                                                    : "Submit Report"}
                                            </PrimaryButton>
                                        </div>
                                    </form>
                                </Dialog.Panel>
                            </Transition.Child>
                        </div>
                    </div>
                </Dialog>
            </Transition>
        </>
    );
}

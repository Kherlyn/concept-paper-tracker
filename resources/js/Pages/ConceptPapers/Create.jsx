import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import InputError from "@/Components/InputError";
import ValidationErrors from "@/Components/ValidationErrors";
import PrimaryButton from "@/Components/PrimaryButton";
import FileUpload from "@/Components/FileUpload";
import { Head, Link, useForm, usePage } from "@inertiajs/react";

export default function Create() {
    const { auth, flash } = usePage().props;
    const { data, setData, post, processing, errors, reset } = useForm({
        department: auth.user.department || "",
        title: "",
        nature_of_request: "regular",
        attachment: null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();

        // Create FormData for file upload
        const formData = new FormData();
        formData.append("department", data.department);
        formData.append("title", data.title);
        formData.append("nature_of_request", data.nature_of_request);
        if (data.attachment) {
            formData.append("attachment", data.attachment);
        }

        post(route("concept-papers.store"), {
            data: formData,
            forceFormData: true,
            onSuccess: () => {
                reset();
            },
        });
    };

    const handleFileUpload = (file) => {
        setData("attachment", file);
    };

    return (
        <AuthenticatedLayout
            header={
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                    <h2 className="text-lg sm:text-xl font-semibold leading-tight text-gray-800">
                        Submit New Concept Paper
                    </h2>
                    <Link
                        href={route("concept-papers.index")}
                        className="text-indigo-600 hover:text-indigo-900 text-sm font-medium whitespace-nowrap"
                    >
                        ← Back to List
                    </Link>
                </div>
            }
        >
            <Head title="Submit Concept Paper" />

            <div className="py-6 sm:py-12">
                <div className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-4 sm:p-6">
                            <ValidationErrors
                                errors={errors}
                                className="mb-6"
                            />

                            <form onSubmit={handleSubmit} className="space-y-6">
                                {/* Requisitioner Name (Read-only) */}
                                <div>
                                    <InputLabel
                                        htmlFor="requisitioner_name"
                                        value="Requisitioner Name"
                                    />
                                    <TextInput
                                        id="requisitioner_name"
                                        type="text"
                                        value={auth.user.name}
                                        className="mt-1 block w-full bg-gray-50"
                                        disabled
                                        readOnly
                                    />
                                    <p className="mt-1 text-xs text-gray-500">
                                        This is automatically set to your
                                        account name
                                    </p>
                                </div>

                                {/* Department */}
                                <div>
                                    <InputLabel
                                        htmlFor="department"
                                        value="Department"
                                    />
                                    <TextInput
                                        id="department"
                                        type="text"
                                        value={data.department}
                                        className="mt-1 block w-full"
                                        onChange={(e) =>
                                            setData(
                                                "department",
                                                e.target.value
                                            )
                                        }
                                        required
                                        autoFocus
                                    />
                                    <InputError
                                        message={errors.department}
                                        className="mt-2"
                                    />
                                </div>

                                {/* Title */}
                                <div>
                                    <InputLabel
                                        htmlFor="title"
                                        value="Concept Paper Title"
                                    />
                                    <textarea
                                        id="title"
                                        value={data.title}
                                        onChange={(e) =>
                                            setData("title", e.target.value)
                                        }
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        rows="3"
                                        required
                                    />
                                    <InputError
                                        message={errors.title}
                                        className="mt-2"
                                    />
                                </div>

                                {/* Nature of Request */}
                                <div>
                                    <InputLabel
                                        htmlFor="nature_of_request"
                                        value="Nature of Request"
                                    />
                                    <select
                                        id="nature_of_request"
                                        value={data.nature_of_request}
                                        onChange={(e) =>
                                            setData(
                                                "nature_of_request",
                                                e.target.value
                                            )
                                        }
                                        className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                    >
                                        <option value="regular">Regular</option>
                                        <option value="urgent">Urgent</option>
                                        <option value="emergency">
                                            Emergency
                                        </option>
                                    </select>
                                    <InputError
                                        message={errors.nature_of_request}
                                        className="mt-2"
                                    />
                                    <p className="mt-1 text-xs text-gray-500">
                                        Select the urgency level for this
                                        request
                                    </p>
                                </div>

                                {/* File Upload */}
                                <div>
                                    <FileUpload
                                        label="Concept Paper Attachment (Optional)"
                                        accept=".pdf"
                                        maxSize={10}
                                        onUpload={handleFileUpload}
                                        error={errors.attachment}
                                        helpText="Upload the concept paper document in PDF format"
                                    />
                                </div>

                                {/* Submit Button */}
                                <div className="flex items-center justify-end gap-4 pt-4 border-t border-gray-200">
                                    <Link
                                        href={route("concept-papers.index")}
                                        className="text-sm text-gray-600 hover:text-gray-900"
                                    >
                                        Cancel
                                    </Link>
                                    <PrimaryButton disabled={processing}>
                                        {processing
                                            ? "Submitting..."
                                            : "Submit Concept Paper"}
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Information Card */}
                    <div className="mt-6 overflow-hidden bg-blue-50 shadow-sm sm:rounded-lg">
                        <div className="p-4 sm:p-6">
                            <h3 className="text-sm font-semibold text-blue-900 mb-2">
                                What happens next?
                            </h3>
                            <ul className="text-xs sm:text-sm text-blue-800 space-y-1 list-disc list-inside">
                                <li>
                                    Your concept paper will be assigned a unique
                                    tracking number
                                </li>
                                <li>
                                    It will enter the 9-step approval workflow
                                    starting with SPS Review
                                </li>
                                <li>
                                    You'll receive notifications as it
                                    progresses through each stage
                                </li>
                                <li>
                                    You can track its status anytime from your
                                    dashboard
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

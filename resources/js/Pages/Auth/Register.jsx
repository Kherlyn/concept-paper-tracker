import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import GuestLayout from "@/Layouts/GuestLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { useState, useEffect } from "react";

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: "",
        email: "",
        password: "",
        password_confirmation: "",
        role: "requisitioner",
        department: "",
        school_year: "",
        student_number: "",
    });

    const [validationMessages, setValidationMessages] = useState({});

    const roleDescriptions = {
        requisitioner: "Submit and track concept papers",
        sps: "School Principal/Supervisor - Initial review and approval",
        vp_acad: "VP Academic Affairs - Academic review and distribution",
        auditor: "Audit review and countersigning",
        accounting: "Voucher and cheque preparation",
    };

    const roleTooltips = {
        requisitioner:
            "Choose this role if you will be submitting concept papers for approval",
        sps: "School Principal/Supervisor role for initial review of submissions",
        vp_acad:
            "VP Academic Affairs role for academic review and distribution",
        auditor: "Auditor role for audit review and countersigning documents",
        accounting: "Accounting role for voucher and cheque preparation",
    };

    // Real-time validation
    useEffect(() => {
        const messages = {};

        // Name validation
        if (data.name && data.name.length < 3) {
            messages.name = "Name should be at least 3 characters";
        } else if (data.name && data.name.length > 255) {
            messages.name = "Name should not exceed 255 characters";
        }

        // Email validation
        if (data.email && !data.email.includes("@")) {
            messages.email = "Please enter a valid email address";
        }

        // Password validation
        if (data.password && data.password.length < 8) {
            messages.password = "Password must be at least 8 characters";
        } else if (data.password && data.password.length > 0) {
            messages.password = "✓ Password meets minimum length";
        }

        // Password confirmation validation
        if (
            data.password_confirmation &&
            data.password !== data.password_confirmation
        ) {
            messages.password_confirmation = "Passwords do not match";
        } else if (
            data.password_confirmation &&
            data.password === data.password_confirmation &&
            data.password.length >= 8
        ) {
            messages.password_confirmation = "✓ Passwords match";
        }

        // School year validation
        if (data.school_year && data.school_year.length > 50) {
            messages.school_year =
                "School year should not exceed 50 characters";
        }

        // Student number validation
        if (data.student_number && data.student_number.length > 50) {
            messages.student_number =
                "Student number should not exceed 50 characters";
        }

        setValidationMessages(messages);
    }, [data]);

    const submit = (e) => {
        e.preventDefault();

        post(route("register"), {
            onFinish: () => reset("password", "password_confirmation"),
        });
    };

    return (
        <GuestLayout>
            <Head title="Register" />

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="name" value="Full Name" />

                    <TextInput
                        id="name"
                        name="name"
                        value={data.name}
                        className="mt-1 block w-full"
                        autoComplete="name"
                        isFocused={true}
                        onChange={(e) => setData("name", e.target.value)}
                        required
                    />

                    <p className="mt-1 text-sm text-gray-600">
                        Enter your complete name as it appears in official
                        records
                    </p>

                    {validationMessages.name && !errors.name && (
                        <p
                            className={`mt-1 text-sm ${
                                validationMessages.name.startsWith("✓")
                                    ? "text-green-600"
                                    : "text-amber-600"
                            }`}
                        >
                            {validationMessages.name}
                        </p>
                    )}

                    <InputError message={errors.name} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="email" value="Email Address" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        onChange={(e) => setData("email", e.target.value)}
                        required
                    />

                    <p className="mt-1 text-sm text-gray-600">
                        Use your institutional or personal email address for
                        account access
                    </p>

                    {validationMessages.email && !errors.email && (
                        <p className="mt-1 text-sm text-amber-600">
                            {validationMessages.email}
                        </p>
                    )}

                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="role" value="Role" />

                    <select
                        id="role"
                        name="role"
                        value={data.role}
                        onChange={(e) => setData("role", e.target.value)}
                        className="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                        required
                        title={roleTooltips[data.role]}
                    >
                        {Object.entries(roleDescriptions).map(
                            ([value, description]) => (
                                <option
                                    key={value}
                                    value={value}
                                    title={roleTooltips[value]}
                                >
                                    {value.replace("_", " ").toUpperCase()}
                                </option>
                            )
                        )}
                    </select>

                    <div className="mt-2 p-3 bg-blue-50 rounded-md border border-blue-200">
                        <p className="text-sm font-medium text-blue-900">
                            {roleDescriptions[data.role]}
                        </p>
                        <p className="mt-1 text-xs text-blue-700">
                            {roleTooltips[data.role]}
                        </p>
                    </div>

                    <InputError message={errors.role} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="department" value="Department" />

                    <TextInput
                        id="department"
                        name="department"
                        value={data.department}
                        className="mt-1 block w-full"
                        autoComplete="organization"
                        onChange={(e) => setData("department", e.target.value)}
                        required
                    />

                    <p className="mt-1 text-sm text-gray-600">
                        Enter your department or organizational unit
                    </p>

                    <InputError message={errors.department} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel
                        htmlFor="school_year"
                        value="School Year (Optional)"
                    />

                    <TextInput
                        id="school_year"
                        name="school_year"
                        value={data.school_year}
                        className="mt-1 block w-full"
                        placeholder="e.g., 2024-2025 or 1st Year"
                        onChange={(e) => setData("school_year", e.target.value)}
                        title="Enter your current academic year (e.g., 2024-2025, 1st Year, 2nd Year)"
                    />

                    <p className="mt-1 text-sm text-gray-600">
                        Enter your current academic year (e.g., 2024-2025, 1st
                        Year, 2nd Year)
                    </p>

                    {validationMessages.school_year && !errors.school_year && (
                        <p className="mt-1 text-sm text-amber-600">
                            {validationMessages.school_year}
                        </p>
                    )}

                    <InputError message={errors.school_year} className="mt-2" />
                </div>

                {data.role === "requisitioner" && (
                    <div className="mt-4">
                        <InputLabel
                            htmlFor="student_number"
                            value="Student Number (Optional)"
                        />

                        <TextInput
                            id="student_number"
                            name="student_number"
                            value={data.student_number}
                            className="mt-1 block w-full"
                            placeholder="e.g., 2024-00001"
                            onChange={(e) =>
                                setData("student_number", e.target.value)
                            }
                            title="Enter your unique student identification number if you are a student"
                        />

                        <p className="mt-1 text-sm text-gray-600">
                            Your unique student identification number (if
                            applicable)
                        </p>

                        {validationMessages.student_number &&
                            !errors.student_number && (
                                <p className="mt-1 text-sm text-amber-600">
                                    {validationMessages.student_number}
                                </p>
                            )}

                        <InputError
                            message={errors.student_number}
                            className="mt-2"
                        />
                    </div>
                )}

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) => setData("password", e.target.value)}
                        required
                    />

                    <p className="mt-1 text-sm text-gray-600">
                        Must be at least 8 characters long
                    </p>

                    {validationMessages.password && !errors.password && (
                        <p
                            className={`mt-1 text-sm ${
                                validationMessages.password.startsWith("✓")
                                    ? "text-green-600"
                                    : "text-amber-600"
                            }`}
                        >
                            {validationMessages.password}
                        </p>
                    )}

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel
                        htmlFor="password_confirmation"
                        value="Confirm Password"
                    />

                    <TextInput
                        id="password_confirmation"
                        type="password"
                        name="password_confirmation"
                        value={data.password_confirmation}
                        className="mt-1 block w-full"
                        autoComplete="new-password"
                        onChange={(e) =>
                            setData("password_confirmation", e.target.value)
                        }
                        required
                    />

                    <p className="mt-1 text-sm text-gray-600">
                        Re-enter your password to confirm
                    </p>

                    {validationMessages.password_confirmation &&
                        !errors.password_confirmation && (
                            <p
                                className={`mt-1 text-sm ${
                                    validationMessages.password_confirmation.startsWith(
                                        "✓"
                                    )
                                        ? "text-green-600"
                                        : "text-amber-600"
                                }`}
                            >
                                {validationMessages.password_confirmation}
                            </p>
                        )}

                    <InputError
                        message={errors.password_confirmation}
                        className="mt-2"
                    />
                </div>

                <div className="mt-4 flex items-center justify-end">
                    <Link
                        href={route("login")}
                        className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Already registered?
                    </Link>

                    <PrimaryButton className="ms-4" disabled={processing}>
                        Register
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}

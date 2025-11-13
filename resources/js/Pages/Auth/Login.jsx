import Checkbox from "@/Components/Checkbox";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import GuestLayout from "@/Layouts/GuestLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import {
    QuestionMarkCircleIcon,
    BookOpenIcon,
    EnvelopeIcon,
    InformationCircleIcon,
} from "@heroicons/react/24/outline";

export default function Login({ status, canResetPassword, systemStatus }) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: "",
        password: "",
        remember: false,
    });

    const submit = (e) => {
        e.preventDefault();

        post(route("login"), {
            onFinish: () => reset("password"),
        });
    };

    return (
        <GuestLayout>
            <Head title="Log in" />

            {/* System Status Notifications */}
            {systemStatus && (
                <div className="mb-4 rounded-lg bg-yellow-50 p-4 border border-yellow-200">
                    <div className="flex">
                        <InformationCircleIcon className="h-5 w-5 text-yellow-400 mr-2" />
                        <div className="text-sm text-yellow-800">
                            {systemStatus}
                        </div>
                    </div>
                </div>
            )}

            {status && (
                <div className="mb-4 text-sm font-medium text-green-600">
                    {status}
                </div>
            )}

            {/* Need Help Section */}
            <div className="mb-6 rounded-lg bg-blue-50 p-4 border border-blue-200">
                <div className="flex items-start">
                    <QuestionMarkCircleIcon className="h-5 w-5 text-blue-600 mr-2 mt-0.5 flex-shrink-0" />
                    <div className="flex-1">
                        <h3 className="text-sm font-semibold text-blue-900 mb-2">
                            Need Help?
                        </h3>
                        <ul className="space-y-2 text-sm text-blue-800">
                            <li className="flex items-center">
                                <BookOpenIcon className="h-4 w-4 mr-2 flex-shrink-0" />
                                <span>
                                    New to the system?{" "}
                                    <a
                                        href="#"
                                        onClick={(e) => {
                                            e.preventDefault();
                                            alert(
                                                "User guide will be available after logging in. Please use your credentials to access the system."
                                            );
                                        }}
                                        className="font-medium underline hover:text-blue-900"
                                    >
                                        View User Guide
                                    </a>{" "}
                                    (available after login)
                                </span>
                            </li>
                            <li className="flex items-center">
                                <EnvelopeIcon className="h-4 w-4 mr-2 flex-shrink-0" />
                                <span>
                                    Support:{" "}
                                    <a
                                        href="mailto:support@conceptpapertracker.local"
                                        className="font-medium underline hover:text-blue-900"
                                    >
                                        support@conceptpapertracker.local
                                    </a>
                                </span>
                            </li>
                            {import.meta.env.DEV && (
                                <li className="text-xs bg-blue-100 p-2 rounded mt-2">
                                    <strong>Development Mode:</strong> Test
                                    accounts available with roles:
                                    requisitioner, sps, vp_acad, auditor,
                                    accounting, admin. Check database seeders
                                    for credentials.
                                </li>
                            )}
                        </ul>
                    </div>
                </div>
            </div>

            <form onSubmit={submit}>
                <div>
                    <InputLabel htmlFor="email" value="Email" />

                    <TextInput
                        id="email"
                        type="email"
                        name="email"
                        value={data.email}
                        className="mt-1 block w-full"
                        autoComplete="username"
                        isFocused={true}
                        onChange={(e) => setData("email", e.target.value)}
                    />

                    <InputError message={errors.email} className="mt-2" />
                </div>

                <div className="mt-4">
                    <InputLabel htmlFor="password" value="Password" />

                    <TextInput
                        id="password"
                        type="password"
                        name="password"
                        value={data.password}
                        className="mt-1 block w-full"
                        autoComplete="current-password"
                        onChange={(e) => setData("password", e.target.value)}
                    />

                    <InputError message={errors.password} className="mt-2" />
                </div>

                <div className="mt-4 block">
                    <label className="flex items-center">
                        <Checkbox
                            name="remember"
                            checked={data.remember}
                            onChange={(e) =>
                                setData("remember", e.target.checked)
                            }
                        />
                        <span className="ms-2 text-sm text-gray-600">
                            Remember me
                        </span>
                    </label>
                </div>

                <div className="mt-4 flex items-center justify-end">
                    {canResetPassword && (
                        <Link
                            href={route("password.request")}
                            className="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Forgot your password?
                        </Link>
                    )}

                    <PrimaryButton className="ms-4" disabled={processing}>
                        Log in
                    </PrimaryButton>
                </div>
            </form>
        </GuestLayout>
    );
}

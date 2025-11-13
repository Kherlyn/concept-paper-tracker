import { Link } from "@inertiajs/react";
import { ArrowRightIcon } from "@heroicons/react/24/outline";

export default function CTASection({ canRegister }) {
    return (
        <section className="py-20 bg-gradient-to-r from-indigo-600 to-purple-600">
            <div className="container mx-auto px-6 text-center">
                <h2 className="text-4xl md:text-5xl font-bold text-white mb-6">
                    Ready to Get Started?
                </h2>
                <p className="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
                    Join the digital transformation of concept paper management.
                    Create your account today and experience streamlined
                    approvals.
                </p>
                {canRegister && (
                    <Link
                        href={route("register")}
                        className="inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-lg hover:bg-gray-50 transition-colors shadow-lg hover:shadow-xl"
                    >
                        Create Your Account
                        <ArrowRightIcon className="ml-2 h-5 w-5" />
                    </Link>
                )}
                <div className="mt-8 flex flex-col sm:flex-row justify-center items-center gap-6 text-indigo-100">
                    <div className="flex items-center">
                        <svg
                            className="h-5 w-5 mr-2"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fillRule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clipRule="evenodd"
                            />
                        </svg>
                        <span>Free to use</span>
                    </div>
                    <div className="flex items-center">
                        <svg
                            className="h-5 w-5 mr-2"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fillRule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clipRule="evenodd"
                            />
                        </svg>
                        <span>Easy setup</span>
                    </div>
                    <div className="flex items-center">
                        <svg
                            className="h-5 w-5 mr-2"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fillRule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clipRule="evenodd"
                            />
                        </svg>
                        <span>Full support</span>
                    </div>
                </div>
            </div>
        </section>
    );
}

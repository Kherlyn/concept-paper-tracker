import { Link } from "@inertiajs/react";
import { DocumentTextIcon } from "@heroicons/react/24/outline";

export default function Footer() {
    const currentYear = new Date().getFullYear();

    return (
        <footer className="bg-gray-900 text-gray-300">
            <div className="container mx-auto px-6 py-12">
                <div className="grid md:grid-cols-4 gap-8">
                    {/* Brand */}
                    <div className="col-span-2 md:col-span-1">
                        <div className="flex items-center space-x-2 mb-4">
                            <DocumentTextIcon className="h-8 w-8 text-indigo-400" />
                            <span className="text-xl font-bold text-white">
                                Concept Paper Tracker
                            </span>
                        </div>
                        <p className="text-sm text-gray-400">
                            Streamlining concept paper approvals with digital
                            workflow management.
                        </p>
                    </div>

                    {/* Quick Links */}
                    <div>
                        <h3 className="text-white font-semibold mb-4">
                            Quick Links
                        </h3>
                        <ul className="space-y-2 text-sm">
                            <li>
                                <Link
                                    href={route("login")}
                                    className="hover:text-indigo-400 transition-colors"
                                >
                                    Login
                                </Link>
                            </li>
                            <li>
                                <Link
                                    href={route("register")}
                                    className="hover:text-indigo-400 transition-colors"
                                >
                                    Register
                                </Link>
                            </li>
                            <li>
                                <a
                                    href="#how-it-works"
                                    className="hover:text-indigo-400 transition-colors"
                                >
                                    How It Works
                                </a>
                            </li>
                        </ul>
                    </div>

                    {/* Resources */}
                    <div>
                        <h3 className="text-white font-semibold mb-4">
                            Resources
                        </h3>
                        <ul className="space-y-2 text-sm">
                            <li>
                                <span className="text-gray-500">
                                    User Guide (Login Required)
                                </span>
                            </li>
                            <li>
                                <span className="text-gray-500">
                                    Documentation
                                </span>
                            </li>
                            <li>
                                <span className="text-gray-500">FAQ</span>
                            </li>
                        </ul>
                    </div>

                    {/* Contact */}
                    <div>
                        <h3 className="text-white font-semibold mb-4">
                            Support
                        </h3>
                        <ul className="space-y-2 text-sm">
                            <li>
                                <a
                                    href="mailto:support@example.com"
                                    className="hover:text-indigo-400 transition-colors"
                                >
                                    support@example.com
                                </a>
                            </li>
                            <li className="text-gray-400">Technical Support</li>
                            <li className="text-gray-400">
                                Monday - Friday, 9AM - 5PM
                            </li>
                        </ul>
                    </div>
                </div>

                {/* Bottom Bar */}
                <div className="border-t border-gray-800 mt-8 pt-8 text-sm text-center text-gray-400">
                    <p>
                        &copy; {currentYear} Concept Paper Tracker. All rights
                        reserved.
                    </p>
                </div>
            </div>
        </footer>
    );
}

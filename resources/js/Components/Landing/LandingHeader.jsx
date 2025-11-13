import { Link } from "@inertiajs/react";
import { DocumentTextIcon } from "@heroicons/react/24/outline";

export default function LandingHeader({ canLogin, canRegister }) {
    return (
        <header className="fixed top-0 w-full bg-white/90 backdrop-blur-sm shadow-sm z-50">
            <nav className="container mx-auto px-6 py-4 flex justify-between items-center">
                <div className="flex items-center space-x-2">
                    <DocumentTextIcon className="h-8 w-8 text-indigo-600" />
                    <span className="text-xl font-bold text-gray-900">
                        Concept Paper Tracker
                    </span>
                </div>
                <div className="flex space-x-4">
                    {canLogin && (
                        <Link
                            href={route("login")}
                            className="px-4 py-2 text-gray-700 hover:text-indigo-600 font-medium transition-colors"
                        >
                            Login
                        </Link>
                    )}
                    {canRegister && (
                        <Link
                            href={route("register")}
                            className="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium transition-colors shadow-sm"
                        >
                            Register
                        </Link>
                    )}
                </div>
            </nav>
        </header>
    );
}

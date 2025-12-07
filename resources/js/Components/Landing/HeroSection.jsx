import { Link } from "@inertiajs/react";
import { ArrowRightIcon } from "@heroicons/react/24/outline";

export default function HeroSection({ canRegister }) {
    const scrollToSection = (id) => {
        const element = document.getElementById(id);
        if (element) {
            element.scrollIntoView({ behavior: "smooth" });
        }
    };

    return (
        <section className="pt-32 pb-20 px-6">
            <div className="container mx-auto text-center">
                <h1 className="text-5xl md:text-6xl font-bold text-gray-900 mb-6 leading-tight">
                    Streamline Your Concept Paper Approvals
                </h1>
                <p className="text-xl md:text-2xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Digital workflow management for concept papers with budget
                    allocation. Track, approve, and manage submissions
                    efficiently through a 10-step automated process.
                </p>
                <div className="flex flex-col sm:flex-row justify-center gap-4">
                    {canRegister && (
                        <Link
                            href={route("register")}
                            className="inline-flex items-center justify-center px-8 py-4 bg-indigo-600 text-white text-lg font-semibold rounded-lg hover:bg-indigo-700 transition-colors shadow-lg hover:shadow-xl"
                        >
                            Get Started
                            <ArrowRightIcon className="ml-2 h-5 w-5" />
                        </Link>
                    )}
                    <button
                        onClick={() => scrollToSection("how-it-works")}
                        className="inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-600 text-lg font-semibold rounded-lg border-2 border-indigo-600 hover:bg-indigo-50 transition-colors"
                    >
                        Learn More
                    </button>
                </div>
            </div>
        </section>
    );
}

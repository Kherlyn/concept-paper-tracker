import { Head, Link } from "@inertiajs/react";
import LandingHeader from "@/Components/Landing/LandingHeader";
import HeroSection from "@/Components/Landing/HeroSection";
import FeaturesSection from "@/Components/Landing/FeaturesSection";
import WorkflowSection from "@/Components/Landing/WorkflowSection";
import RolesSection from "@/Components/Landing/RolesSection";
import UseCasesSection from "@/Components/Landing/UseCasesSection";
import CTASection from "@/Components/Landing/CTASection";
import Footer from "@/Components/Landing/Footer";

export default function Landing({ canLogin, canRegister }) {
    return (
        <div className="min-h-screen bg-gradient-to-b from-gray-50 to-white">
            <Head title="Concept Paper Tracker - Streamline Your Approvals" />

            <LandingHeader canLogin={canLogin} canRegister={canRegister} />
            <HeroSection canRegister={canRegister} />
            <FeaturesSection />
            <WorkflowSection />
            <RolesSection />
            <UseCasesSection />
            <CTASection canRegister={canRegister} />
            <Footer />
        </div>
    );
}

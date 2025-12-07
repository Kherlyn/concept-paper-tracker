import {
    ClockIcon,
    BellIcon,
    ChartBarIcon,
    ShieldCheckIcon,
    DocumentCheckIcon,
    ArrowPathIcon,
} from "@heroicons/react/24/outline";

const features = [
    {
        title: "Digital Workflow",
        description:
            "Automated routing through 10-step approval process with real-time tracking",
        icon: ArrowPathIcon,
    },
    {
        title: "Submission Tracking",
        description:
            "Monitor your concept papers from submission to budget release",
        icon: DocumentCheckIcon,
    },
    {
        title: "Smart Notifications",
        description:
            "Stay informed with email alerts for assignments, approvals, and deadlines",
        icon: BellIcon,
    },
    {
        title: "Deadline Management",
        description:
            "Automatic tracking of processing times with overdue alerts",
        icon: ClockIcon,
    },
    {
        title: "Comprehensive Reports",
        description: "Generate detailed reports and export data for analysis",
        icon: ChartBarIcon,
    },
    {
        title: "Audit Trail",
        description:
            "Complete history of all actions and approvals for accountability",
        icon: ShieldCheckIcon,
    },
];

function FeatureCard({ title, description, icon: Icon }) {
    return (
        <div className="p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100">
            <div className="flex items-center mb-4">
                <div className="p-3 bg-indigo-100 rounded-lg">
                    <Icon className="h-6 w-6 text-indigo-600" />
                </div>
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2">
                {title}
            </h3>
            <p className="text-gray-600">{description}</p>
        </div>
    );
}

export default function FeaturesSection() {
    return (
        <section className="py-20 bg-gray-50">
            <div className="container mx-auto px-6">
                <h2 className="text-4xl font-bold text-center text-gray-900 mb-4">
                    Key Features
                </h2>
                <p className="text-xl text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                    Everything you need to manage concept paper approvals
                    efficiently
                </p>
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    {features.map((feature, index) => (
                        <FeatureCard key={index} {...feature} />
                    ))}
                </div>
            </div>
        </section>
    );
}

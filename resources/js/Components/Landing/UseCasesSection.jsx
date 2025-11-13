import {
    DocumentTextIcon,
    CheckBadgeIcon,
    ChartBarIcon,
} from "@heroicons/react/24/outline";

const useCases = [
    {
        title: "For Requisitioners",
        icon: DocumentTextIcon,
        scenario: "Submit Your Concept Paper",
        description:
            "Maria, a faculty member, needs to submit a concept paper for a research project. She logs into the system, fills out the digital form, uploads her PDF, and submits it. She receives a tracking number and can monitor the approval status in real-time as it moves through each stage.",
        benefits: [
            "No more physical paper routing",
            "Real-time status tracking",
            "Email notifications at each stage",
            "Complete audit trail",
        ],
    },
    {
        title: "For Approvers",
        icon: CheckBadgeIcon,
        scenario: "Review and Approve Efficiently",
        description:
            "Dr. Santos, the VP Academic Affairs, receives a notification about a pending concept paper. He reviews the submission online, adds his remarks, and approves it with a single click. The system automatically routes it to the next stage and notifies the auditor.",
        benefits: [
            "Centralized approval dashboard",
            "Clear deadline indicators",
            "Easy document review",
            "One-click approvals",
        ],
    },
    {
        title: "For Administrators",
        icon: ChartBarIcon,
        scenario: "Monitor and Report",
        description:
            "Admin Jane needs to generate a monthly report on concept paper processing times. She accesses the reports dashboard, selects the date range, and exports a comprehensive CSV with all submissions, their current status, and processing times for each stage.",
        benefits: [
            "Comprehensive reporting tools",
            "User management interface",
            "System-wide visibility",
            "Data export capabilities",
        ],
    },
];

function UseCaseCard({ title, icon: Icon, scenario, description, benefits }) {
    return (
        <div className="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div className="p-6 bg-gradient-to-r from-indigo-50 to-purple-50">
                <div className="flex items-center mb-3">
                    <div className="p-2 bg-indigo-600 rounded-lg">
                        <Icon className="h-6 w-6 text-white" />
                    </div>
                    <h3 className="ml-3 text-xl font-semibold text-gray-900">
                        {title}
                    </h3>
                </div>
                <p className="text-lg font-medium text-indigo-900">
                    {scenario}
                </p>
            </div>
            <div className="p-6">
                <p className="text-gray-600 mb-4">{description}</p>
                <div className="space-y-2">
                    <p className="text-sm font-semibold text-gray-900">
                        Key Benefits:
                    </p>
                    <ul className="space-y-1">
                        {benefits.map((benefit, index) => (
                            <li
                                key={index}
                                className="flex items-start text-sm text-gray-600"
                            >
                                <span className="text-green-500 mr-2">✓</span>
                                {benefit}
                            </li>
                        ))}
                    </ul>
                </div>
            </div>
        </div>
    );
}

export default function UseCasesSection() {
    return (
        <section className="py-20 bg-white">
            <div className="container mx-auto px-6">
                <h2 className="text-4xl font-bold text-center text-gray-900 mb-4">
                    How It Benefits You
                </h2>
                <p className="text-xl text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                    Real-world scenarios showing how the system streamlines
                    workflows for everyone
                </p>
                <div className="grid lg:grid-cols-3 gap-8">
                    {useCases.map((useCase, index) => (
                        <UseCaseCard key={index} {...useCase} />
                    ))}
                </div>
            </div>
        </section>
    );
}

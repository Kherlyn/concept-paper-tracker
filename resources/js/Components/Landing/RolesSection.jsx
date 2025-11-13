import {
    UserIcon,
    ShieldCheckIcon,
    AcademicCapIcon,
    ClipboardDocumentCheckIcon,
    CalculatorIcon,
    CogIcon,
} from "@heroicons/react/24/outline";

const roles = [
    {
        name: "Requisitioner",
        description:
            "Submit and track concept papers through the approval process",
        icon: UserIcon,
        color: "blue",
    },
    {
        name: "SPS",
        description:
            "School Principal/Supervisor - Initial review and approval",
        icon: ShieldCheckIcon,
        color: "green",
    },
    {
        name: "VP Academic",
        description: "VP Academic Affairs - Academic review and distribution",
        icon: AcademicCapIcon,
        color: "purple",
    },
    {
        name: "Auditor",
        description:
            "Audit review, countersigning, and compliance verification",
        icon: ClipboardDocumentCheckIcon,
        color: "orange",
    },
    {
        name: "Accounting",
        description: "Voucher and cheque preparation, budget release",
        icon: CalculatorIcon,
        color: "pink",
    },
    {
        name: "Admin",
        description: "System management, user administration, and reporting",
        icon: CogIcon,
        color: "gray",
    },
];

const colorClasses = {
    blue: "bg-blue-100 text-blue-600",
    green: "bg-green-100 text-green-600",
    purple: "bg-purple-100 text-purple-600",
    orange: "bg-orange-100 text-orange-600",
    pink: "bg-pink-100 text-pink-600",
    gray: "bg-gray-100 text-gray-600",
};

function RoleCard({ name, description, icon: Icon, color }) {
    return (
        <div className="p-6 bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow border border-gray-100">
            <div className="flex items-center mb-4">
                <div className={`p-3 rounded-lg ${colorClasses[color]}`}>
                    <Icon className="h-6 w-6" />
                </div>
            </div>
            <h3 className="text-lg font-semibold text-gray-900 mb-2">{name}</h3>
            <p className="text-gray-600">{description}</p>
        </div>
    );
}

export default function RolesSection() {
    return (
        <section className="py-20 bg-gray-50">
            <div className="container mx-auto px-6">
                <h2 className="text-4xl font-bold text-center text-gray-900 mb-4">
                    User Roles
                </h2>
                <p className="text-xl text-gray-600 text-center mb-12 max-w-2xl mx-auto">
                    Six distinct roles working together to ensure efficient
                    approval workflows
                </p>
                <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {roles.map((role, index) => (
                        <RoleCard key={index} {...role} />
                    ))}
                </div>
            </div>
        </section>
    );
}

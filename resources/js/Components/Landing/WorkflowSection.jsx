import WorkflowVisualization from "@/Components/WorkflowVisualization";

export default function WorkflowSection() {
    return (
        <section id="how-it-works" className="py-20 bg-white">
            <div className="container mx-auto px-6">
                <div className="text-center mb-12">
                    <h2 className="text-4xl font-bold text-gray-900 mb-4">
                        9-Step Approval Process
                    </h2>
                    <p className="text-xl text-gray-600 max-w-2xl mx-auto">
                        From submission to budget release, every step is tracked
                        and managed automatically
                    </p>
                </div>
                <div className="max-w-3xl mx-auto">
                    <WorkflowVisualization />
                </div>
            </div>
        </section>
    );
}

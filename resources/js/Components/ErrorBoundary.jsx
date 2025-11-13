import { Component } from "react";

class ErrorBoundary extends Component {
    constructor(props) {
        super(props);
        this.state = { hasError: false, error: null, errorInfo: null };
    }

    static getDerivedStateFromError(error) {
        return { hasError: true };
    }

    componentDidCatch(error, errorInfo) {
        console.error("Error caught by boundary:", error, errorInfo);
        this.setState({
            error,
            errorInfo,
        });

        // You can also log the error to an error reporting service here
        // Example: logErrorToService(error, errorInfo);
    }

    handleReset = () => {
        this.setState({ hasError: false, error: null, errorInfo: null });
    };

    render() {
        if (this.state.hasError) {
            return (
                <div className="min-h-screen flex items-center justify-center bg-gray-100 px-4">
                    <div className="max-w-md w-full bg-white shadow-lg rounded-lg p-6">
                        <div className="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                            <svg
                                className="w-6 h-6 text-red-600"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <h2 className="mt-4 text-xl font-semibold text-center text-gray-900">
                            Something went wrong
                        </h2>
                        <p className="mt-2 text-sm text-center text-gray-600">
                            We're sorry, but something unexpected happened.
                            Please try again.
                        </p>

                        {process.env.NODE_ENV === "development" &&
                            this.state.error && (
                                <details className="mt-4 p-4 bg-gray-50 rounded-md text-xs">
                                    <summary className="cursor-pointer font-medium text-gray-700">
                                        Error Details (Development Only)
                                    </summary>
                                    <div className="mt-2 space-y-2">
                                        <div>
                                            <strong className="text-red-600">
                                                Error:
                                            </strong>
                                            <pre className="mt-1 whitespace-pre-wrap text-gray-800">
                                                {this.state.error.toString()}
                                            </pre>
                                        </div>
                                        {this.state.errorInfo && (
                                            <div>
                                                <strong className="text-red-600">
                                                    Stack Trace:
                                                </strong>
                                                <pre className="mt-1 whitespace-pre-wrap text-gray-600 overflow-auto max-h-48">
                                                    {
                                                        this.state.errorInfo
                                                            .componentStack
                                                    }
                                                </pre>
                                            </div>
                                        )}
                                    </div>
                                </details>
                            )}

                        <div className="mt-6 flex gap-3">
                            <button
                                onClick={this.handleReset}
                                className="flex-1 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Try Again
                            </button>
                            <button
                                onClick={() =>
                                    (window.location.href = "/dashboard")
                                }
                                className="flex-1 px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500"
                            >
                                Go to Dashboard
                            </button>
                        </div>
                    </div>
                </div>
            );
        }

        return this.props.children;
    }
}

export default ErrorBoundary;

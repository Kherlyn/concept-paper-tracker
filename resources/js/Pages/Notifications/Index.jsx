import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router } from "@inertiajs/react";
import { useState } from "react";

export default function Index({ notifications, unread_count }) {
    const [selectedType, setSelectedType] = useState("all");

    const handleMarkAsRead = (notificationId) => {
        router.post(
            route("notifications.mark-read", notificationId),
            {},
            {
                preserveScroll: true,
            }
        );
    };

    const handleMarkAllAsRead = () => {
        router.post(
            route("notifications.mark-all-read"),
            {},
            {
                preserveScroll: true,
            }
        );
    };

    const handleDelete = (notificationId) => {
        if (confirm("Are you sure you want to delete this notification?")) {
            router.delete(route("notifications.destroy", notificationId), {
                preserveScroll: true,
            });
        }
    };

    const handleDeleteAllRead = () => {
        if (
            confirm(
                "Are you sure you want to delete all read notifications? This action cannot be undone."
            )
        ) {
            router.delete(route("notifications.delete-all-read"), {
                preserveScroll: true,
            });
        }
    };

    const getNotificationIcon = (type) => {
        switch (type) {
            case "App\\Notifications\\StageAssignedNotification":
                return (
                    <div className="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <svg
                            className="h-6 w-6 text-blue-600"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                        </svg>
                    </div>
                );
            case "App\\Notifications\\StageOverdueNotification":
                return (
                    <div className="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                        <svg
                            className="h-6 w-6 text-red-600"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fillRule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                clipRule="evenodd"
                            />
                        </svg>
                    </div>
                );
            case "App\\Notifications\\PaperCompletedNotification":
                return (
                    <div className="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center">
                        <svg
                            className="h-6 w-6 text-green-600"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fillRule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clipRule="evenodd"
                            />
                        </svg>
                    </div>
                );
            case "App\\Notifications\\PaperReturnedNotification":
                return (
                    <div className="flex-shrink-0 w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center">
                        <svg
                            className="h-6 w-6 text-orange-600"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path
                                fillRule="evenodd"
                                d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                                clipRule="evenodd"
                            />
                        </svg>
                    </div>
                );
            default:
                return (
                    <div className="flex-shrink-0 w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <svg
                            className="h-6 w-6 text-gray-600"
                            fill="currentColor"
                            viewBox="0 0 20 20"
                        >
                            <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                        </svg>
                    </div>
                );
        }
    };

    const getNotificationTypeName = (type) => {
        switch (type) {
            case "App\\Notifications\\StageAssignedNotification":
                return "Stage Assigned";
            case "App\\Notifications\\StageOverdueNotification":
                return "Overdue";
            case "App\\Notifications\\PaperCompletedNotification":
                return "Completed";
            case "App\\Notifications\\PaperReturnedNotification":
                return "Returned";
            default:
                return "Notification";
        }
    };

    const formatTimestamp = (timestamp) => {
        const date = new Date(timestamp);
        return date.toLocaleString("en-US", {
            year: "numeric",
            month: "short",
            day: "numeric",
            hour: "2-digit",
            minute: "2-digit",
        });
    };

    // Filter notifications based on selected type
    const filteredNotifications = notifications.data.filter((notification) => {
        if (selectedType === "all") return true;
        if (selectedType === "unread") return !notification.read_at;
        if (selectedType === "read") return notification.read_at;
        return notification.type === selectedType;
    });

    // Group notifications by read/unread
    const unreadNotifications = filteredNotifications.filter((n) => !n.read_at);
    const readNotifications = filteredNotifications.filter((n) => n.read_at);

    return (
        <AuthenticatedLayout
            header={
                <div className="flex items-center justify-between">
                    <h2 className="text-xl font-semibold leading-tight text-gray-800">
                        Notifications
                    </h2>
                    <div className="flex items-center space-x-3">
                        {unread_count > 0 && (
                            <button
                                onClick={handleMarkAllAsRead}
                                className="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Mark All as Read
                            </button>
                        )}
                        {readNotifications.length > 0 && (
                            <button
                                onClick={handleDeleteAllRead}
                                className="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Delete All Read
                            </button>
                        )}
                    </div>
                </div>
            }
        >
            <Head title="Notifications" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    {/* Filter Tabs */}
                    <div className="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="border-b border-gray-200">
                            <nav className="flex -mb-px">
                                <button
                                    onClick={() => setSelectedType("all")}
                                    className={`px-6 py-4 text-sm font-medium border-b-2 transition ${
                                        selectedType === "all"
                                            ? "border-indigo-500 text-indigo-600"
                                            : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    }`}
                                >
                                    All
                                    <span className="ml-2 px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-600">
                                        {notifications.data.length}
                                    </span>
                                </button>
                                <button
                                    onClick={() => setSelectedType("unread")}
                                    className={`px-6 py-4 text-sm font-medium border-b-2 transition ${
                                        selectedType === "unread"
                                            ? "border-indigo-500 text-indigo-600"
                                            : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    }`}
                                >
                                    Unread
                                    {unread_count > 0 && (
                                        <span className="ml-2 px-2 py-1 text-xs rounded-full bg-red-100 text-red-600">
                                            {unread_count}
                                        </span>
                                    )}
                                </button>
                                <button
                                    onClick={() => setSelectedType("read")}
                                    className={`px-6 py-4 text-sm font-medium border-b-2 transition ${
                                        selectedType === "read"
                                            ? "border-indigo-500 text-indigo-600"
                                            : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    }`}
                                >
                                    Read
                                </button>
                                <button
                                    onClick={() =>
                                        setSelectedType(
                                            "App\\Notifications\\StageAssignedNotification"
                                        )
                                    }
                                    className={`px-6 py-4 text-sm font-medium border-b-2 transition ${
                                        selectedType ===
                                        "App\\Notifications\\StageAssignedNotification"
                                            ? "border-indigo-500 text-indigo-600"
                                            : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    }`}
                                >
                                    Assignments
                                </button>
                                <button
                                    onClick={() =>
                                        setSelectedType(
                                            "App\\Notifications\\StageOverdueNotification"
                                        )
                                    }
                                    className={`px-6 py-4 text-sm font-medium border-b-2 transition ${
                                        selectedType ===
                                        "App\\Notifications\\StageOverdueNotification"
                                            ? "border-indigo-500 text-indigo-600"
                                            : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                                    }`}
                                >
                                    Overdue
                                </button>
                            </nav>
                        </div>
                    </div>

                    {/* Notifications List */}
                    {filteredNotifications.length === 0 ? (
                        <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div className="p-12 text-center">
                                <svg
                                    className="mx-auto h-12 w-12 text-gray-400"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        strokeLinecap="round"
                                        strokeLinejoin="round"
                                        strokeWidth={2}
                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                                    />
                                </svg>
                                <h3 className="mt-2 text-sm font-medium text-gray-900">
                                    No notifications
                                </h3>
                                <p className="mt-1 text-sm text-gray-500">
                                    {selectedType === "all"
                                        ? "You don't have any notifications yet."
                                        : `No ${
                                              selectedType === "unread"
                                                  ? "unread"
                                                  : selectedType === "read"
                                                  ? "read"
                                                  : getNotificationTypeName(
                                                        selectedType
                                                    ).toLowerCase()
                                          } notifications.`}
                                </p>
                            </div>
                        </div>
                    ) : (
                        <>
                            {/* Unread Notifications */}
                            {unreadNotifications.length > 0 && (
                                <div className="mb-6">
                                    <h3 className="text-sm font-semibold text-gray-700 mb-3 px-1">
                                        Unread ({unreadNotifications.length})
                                    </h3>
                                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-200">
                                        {unreadNotifications.map(
                                            (notification) => (
                                                <div
                                                    key={notification.id}
                                                    className="p-6 bg-blue-50 hover:bg-blue-100 transition"
                                                >
                                                    <div className="flex space-x-4">
                                                        {getNotificationIcon(
                                                            notification.type
                                                        )}
                                                        <div className="flex-1 min-w-0">
                                                            <div className="flex items-start justify-between">
                                                                <div className="flex-1">
                                                                    <p className="text-sm font-medium text-gray-900">
                                                                        {
                                                                            notification
                                                                                .data
                                                                                .message
                                                                        }
                                                                    </p>
                                                                    <div className="mt-1 flex items-center space-x-2">
                                                                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                            {getNotificationTypeName(
                                                                                notification.type
                                                                            )}
                                                                        </span>
                                                                        <span className="text-xs text-gray-500">
                                                                            {formatTimestamp(
                                                                                notification.created_at
                                                                            )}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div className="ml-4 flex items-center space-x-3">
                                                                    <button
                                                                        onClick={() =>
                                                                            handleMarkAsRead(
                                                                                notification.id
                                                                            )
                                                                        }
                                                                        className="text-sm text-indigo-600 hover:text-indigo-800 font-medium whitespace-nowrap"
                                                                    >
                                                                        Mark as
                                                                        read
                                                                    </button>
                                                                    <button
                                                                        onClick={() =>
                                                                            handleDelete(
                                                                                notification.id
                                                                            )
                                                                        }
                                                                        className="text-sm text-red-600 hover:text-red-800 font-medium whitespace-nowrap"
                                                                        title="Delete notification"
                                                                    >
                                                                        <svg
                                                                            className="h-5 w-5"
                                                                            fill="none"
                                                                            viewBox="0 0 24 24"
                                                                            stroke="currentColor"
                                                                        >
                                                                            <path
                                                                                strokeLinecap="round"
                                                                                strokeLinejoin="round"
                                                                                strokeWidth={
                                                                                    2
                                                                                }
                                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                            />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            )
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* Read Notifications */}
                            {readNotifications.length > 0 && (
                                <div>
                                    <h3 className="text-sm font-semibold text-gray-700 mb-3 px-1">
                                        Read ({readNotifications.length})
                                    </h3>
                                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg divide-y divide-gray-200">
                                        {readNotifications.map(
                                            (notification) => (
                                                <div
                                                    key={notification.id}
                                                    className="p-6 hover:bg-gray-50 transition"
                                                >
                                                    <div className="flex space-x-4">
                                                        {getNotificationIcon(
                                                            notification.type
                                                        )}
                                                        <div className="flex-1 min-w-0">
                                                            <div className="flex items-start justify-between">
                                                                <div className="flex-1">
                                                                    <p className="text-sm text-gray-700">
                                                                        {
                                                                            notification
                                                                                .data
                                                                                .message
                                                                        }
                                                                    </p>
                                                                    <div className="mt-1 flex items-center space-x-2">
                                                                        <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-600">
                                                                            {getNotificationTypeName(
                                                                                notification.type
                                                                            )}
                                                                        </span>
                                                                        <span className="text-xs text-gray-500">
                                                                            {formatTimestamp(
                                                                                notification.created_at
                                                                            )}
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <button
                                                                    onClick={() =>
                                                                        handleDelete(
                                                                            notification.id
                                                                        )
                                                                    }
                                                                    className="ml-4 text-sm text-red-600 hover:text-red-800 font-medium whitespace-nowrap"
                                                                    title="Delete notification"
                                                                >
                                                                    <svg
                                                                        className="h-5 w-5"
                                                                        fill="none"
                                                                        viewBox="0 0 24 24"
                                                                        stroke="currentColor"
                                                                    >
                                                                        <path
                                                                            strokeLinecap="round"
                                                                            strokeLinejoin="round"
                                                                            strokeWidth={
                                                                                2
                                                                            }
                                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                                        />
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            )
                                        )}
                                    </div>
                                </div>
                            )}
                        </>
                    )}

                    {/* Pagination */}
                    {notifications.links && notifications.links.length > 3 && (
                        <div className="mt-6 flex items-center justify-between bg-white px-4 py-3 sm:px-6 shadow-sm sm:rounded-lg">
                            <div className="flex flex-1 justify-between sm:hidden">
                                {notifications.prev_page_url && (
                                    <a
                                        href={notifications.prev_page_url}
                                        className="relative inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Previous
                                    </a>
                                )}
                                {notifications.next_page_url && (
                                    <a
                                        href={notifications.next_page_url}
                                        className="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                                    >
                                        Next
                                    </a>
                                )}
                            </div>
                            <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                                <div>
                                    <p className="text-sm text-gray-700">
                                        Showing{" "}
                                        <span className="font-medium">
                                            {notifications.from || 0}
                                        </span>{" "}
                                        to{" "}
                                        <span className="font-medium">
                                            {notifications.to || 0}
                                        </span>{" "}
                                        of{" "}
                                        <span className="font-medium">
                                            {notifications.total || 0}
                                        </span>{" "}
                                        results
                                    </p>
                                </div>
                                <div>
                                    <nav className="isolate inline-flex -space-x-px rounded-md shadow-sm">
                                        {notifications.links.map(
                                            (link, index) => (
                                                <a
                                                    key={index}
                                                    href={link.url || "#"}
                                                    className={`relative inline-flex items-center px-4 py-2 text-sm font-medium ${
                                                        link.active
                                                            ? "z-10 bg-indigo-600 text-white focus:z-20"
                                                            : "bg-white text-gray-700 hover:bg-gray-50"
                                                    } ${
                                                        index === 0
                                                            ? "rounded-l-md"
                                                            : ""
                                                    } ${
                                                        index ===
                                                        notifications.links
                                                            .length -
                                                            1
                                                            ? "rounded-r-md"
                                                            : ""
                                                    } ${
                                                        !link.url
                                                            ? "cursor-not-allowed opacity-50"
                                                            : ""
                                                    } border border-gray-300`}
                                                    dangerouslySetInnerHTML={{
                                                        __html: link.label,
                                                    }}
                                                />
                                            )
                                        )}
                                    </nav>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}

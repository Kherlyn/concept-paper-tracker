import { useState } from "react";
import { Link, router } from "@inertiajs/react";
import { Transition } from "@headlessui/react";

export default function NotificationBell({ notifications = [] }) {
    const [isOpen, setIsOpen] = useState(false);

    const handleMarkAsRead = (notificationId, e) => {
        e.preventDefault();
        e.stopPropagation();

        router.post(
            route("notifications.mark-read", notificationId),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    // Notification will be updated via page props
                },
            }
        );
    };

    const handleMarkAllAsRead = (e) => {
        e.preventDefault();

        router.post(
            route("notifications.mark-all-read"),
            {},
            {
                preserveScroll: true,
                onSuccess: () => {
                    setIsOpen(false);
                },
            }
        );
    };

    const getNotificationIcon = (type) => {
        switch (type) {
            case "App\\Notifications\\StageAssignedNotification":
                return (
                    <svg
                        className="h-5 w-5 text-blue-500"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                );
            case "App\\Notifications\\StageOverdueNotification":
                return (
                    <svg
                        className="h-5 w-5 text-red-500"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fillRule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clipRule="evenodd"
                        />
                    </svg>
                );
            case "App\\Notifications\\PaperCompletedNotification":
                return (
                    <svg
                        className="h-5 w-5 text-green-500"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fillRule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clipRule="evenodd"
                        />
                    </svg>
                );
            case "App\\Notifications\\PaperReturnedNotification":
                return (
                    <svg
                        className="h-5 w-5 text-orange-500"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            fillRule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clipRule="evenodd"
                        />
                    </svg>
                );
            default:
                return (
                    <svg
                        className="h-5 w-5 text-gray-500"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path d="M10 2a6 6 0 00-6 6v3.586l-.707.707A1 1 0 004 14h12a1 1 0 00.707-1.707L16 11.586V8a6 6 0 00-6-6zM10 18a3 3 0 01-3-3h6a3 3 0 01-3 3z" />
                    </svg>
                );
        }
    };

    const formatTimestamp = (timestamp) => {
        const date = new Date(timestamp);
        const now = new Date();
        const diffMs = now - date;
        const diffMins = Math.floor(diffMs / 60000);
        const diffHours = Math.floor(diffMs / 3600000);
        const diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) return "Just now";
        if (diffMins < 60) return `${diffMins}m ago`;
        if (diffHours < 24) return `${diffHours}h ago`;
        if (diffDays < 7) return `${diffDays}d ago`;
        return date.toLocaleDateString();
    };

    const unreadCount = notifications.filter((n) => !n.read_at).length;
    const displayNotifications = notifications.slice(0, 5);

    return (
        <div className="relative">
            {/* Bell Icon Button */}
            <button
                onClick={() => setIsOpen(!isOpen)}
                className="relative rounded-md p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition"
                aria-label={
                    unreadCount > 0
                        ? `Notifications: ${unreadCount} unread`
                        : "Notifications"
                }
                aria-expanded={isOpen}
                aria-haspopup="true"
            >
                <svg
                    className="h-6 w-6"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                    aria-hidden="true"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
                    />
                </svg>
                {/* Unread Count Badge */}
                {unreadCount > 0 && (
                    <span
                        className="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full"
                        aria-hidden="true"
                    >
                        {unreadCount > 99 ? "99+" : unreadCount}
                    </span>
                )}
            </button>

            {/* Backdrop */}
            {isOpen && (
                <div
                    className="fixed inset-0 z-40"
                    onClick={() => setIsOpen(false)}
                    aria-hidden="true"
                />
            )}

            {/* Dropdown Panel */}
            <Transition
                show={isOpen}
                enter="transition ease-out duration-200"
                enterFrom="opacity-0 scale-95"
                enterTo="opacity-100 scale-100"
                leave="transition ease-in duration-75"
                leaveFrom="opacity-100 scale-100"
                leaveTo="opacity-0 scale-95"
            >
                <div
                    className="absolute right-0 z-50 mt-2 w-80 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                    role="menu"
                    aria-label="Notifications menu"
                >
                    {/* Header */}
                    <div className="p-4 border-b border-gray-200">
                        <div className="flex items-center justify-between">
                            <h3
                                id="notifications-heading"
                                className="text-sm font-semibold text-gray-900"
                            >
                                Notifications
                            </h3>
                            {unreadCount > 0 && (
                                <button
                                    onClick={handleMarkAllAsRead}
                                    className="text-xs text-indigo-600 hover:text-indigo-800 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded px-2 py-1"
                                    aria-label="Mark all notifications as read"
                                >
                                    Mark all as read
                                </button>
                            )}
                        </div>
                    </div>

                    {/* Notifications List */}
                    <div
                        className="max-h-96 overflow-y-auto"
                        role="list"
                        aria-labelledby="notifications-heading"
                    >
                        {displayNotifications.length === 0 ? (
                            <div
                                className="p-4 text-center text-sm text-gray-500"
                                role="status"
                            >
                                No notifications
                            </div>
                        ) : (
                            displayNotifications.map((notification) => (
                                <div
                                    key={notification.id}
                                    className={`p-4 border-b border-gray-100 hover:bg-gray-50 transition ${
                                        !notification.read_at
                                            ? "bg-blue-50"
                                            : ""
                                    }`}
                                    role="listitem"
                                >
                                    <div className="flex space-x-3">
                                        {/* Notification Icon */}
                                        <div
                                            className="flex-shrink-0"
                                            aria-hidden="true"
                                        >
                                            {getNotificationIcon(
                                                notification.type
                                            )}
                                        </div>
                                        {/* Notification Content */}
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm text-gray-900">
                                                {notification.data.message}
                                            </p>
                                            <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mt-1">
                                                {/* Timestamp */}
                                                <p className="text-xs text-gray-500">
                                                    <time
                                                        dateTime={
                                                            notification.created_at
                                                        }
                                                    >
                                                        {formatTimestamp(
                                                            notification.created_at
                                                        )}
                                                    </time>
                                                </p>
                                                {/* Mark as Read Button */}
                                                {!notification.read_at && (
                                                    <button
                                                        onClick={(e) =>
                                                            handleMarkAsRead(
                                                                notification.id,
                                                                e
                                                            )
                                                        }
                                                        className="text-xs text-indigo-600 hover:text-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded px-2 py-1"
                                                        aria-label="Mark this notification as read"
                                                    >
                                                        Mark as read
                                                    </button>
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>

                    {/* Footer - View All Link */}
                    <div className="p-3 border-t border-gray-200 bg-gray-50">
                        <Link
                            href={route("notifications.index")}
                            className="block text-center text-sm text-indigo-600 hover:text-indigo-800 font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded px-2 py-1"
                            onClick={() => setIsOpen(false)}
                            role="menuitem"
                        >
                            View all notifications
                        </Link>
                    </div>
                </div>
            </Transition>
        </div>
    );
}

import ApplicationLogo from "@/Components/ApplicationLogo";
import Dropdown from "@/Components/Dropdown";
import NavLink from "@/Components/NavLink";
import ResponsiveNavLink from "@/Components/ResponsiveNavLink";
import NotificationBell from "@/Components/NotificationBell";
import Toast from "@/Components/Toast";
import ErrorBoundary from "@/Components/ErrorBoundary";
import useHttpErrorHandler from "@/Hooks/useHttpErrorHandler";
import { Link, usePage } from "@inertiajs/react";
import { useState, useMemo } from "react";
import { BookOpenIcon } from "@heroicons/react/24/outline";

export default function AuthenticatedLayout({ header, children }) {
    const {
        auth,
        unreadNotificationsCount = 0,
        recentNotifications = [],
    } = usePage().props;
    const user = auth.user;

    const [showingNavigationDropdown, setShowingNavigationDropdown] =
        useState(false);

    // Handle HTTP errors globally
    useHttpErrorHandler();

    // Role-based navigation items
    const navigationItems = useMemo(() => {
        const items = [
            {
                href: route("dashboard"),
                label: "Dashboard",
                roles: [
                    "requisitioner",
                    "sps",
                    "vp_acad",
                    "auditor",
                    "accounting",
                    "admin",
                ],
            },
        ];

        // Requisitioner-specific items
        if (user.role === "requisitioner") {
            items.push(
                {
                    href: route("concept-papers.create"),
                    label: "Submit Paper",
                    roles: ["requisitioner"],
                },
                {
                    href: route("concept-papers.index"),
                    label: "My Papers",
                    roles: ["requisitioner"],
                }
            );
        }

        // Approver-specific items
        if (["sps", "vp_acad", "auditor", "accounting"].includes(user.role)) {
            items.push({
                href: route("concept-papers.index"),
                label: "Pending Approvals",
                roles: ["sps", "vp_acad", "auditor", "accounting"],
            });
        }

        // Admin-specific items
        if (user.role === "admin") {
            items.push(
                {
                    href: route("concept-papers.index"),
                    label: "All Papers",
                    roles: ["admin"],
                },
                {
                    href: route("admin.users"),
                    label: "User Management",
                    roles: ["admin"],
                },
                {
                    href: route("admin.reports"),
                    label: "Reports",
                    roles: ["admin"],
                }
            );
        }

        // User Guide - accessible to all authenticated users
        items.push({
            href: route("user-guide"),
            label: "User Guide",
            icon: BookOpenIcon,
            roles: [
                "requisitioner",
                "sps",
                "vp_acad",
                "auditor",
                "accounting",
                "admin",
            ],
        });

        return items.filter((item) => item.roles.includes(user.role));
    }, [user.role]);

    return (
        <ErrorBoundary>
            <Toast />
            <a href="#main-content" className="skip-to-main">
                Skip to main content
            </a>
            <div className="min-h-screen bg-gray-100">
                <nav
                    className="border-b border-gray-100 bg-white"
                    role="navigation"
                    aria-label="Main navigation"
                >
                    <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div className="flex h-16 justify-between">
                            <div className="flex">
                                <div className="flex shrink-0 items-center">
                                    <Link href="/">
                                        <ApplicationLogo className="block h-9 w-auto fill-current text-gray-800" />
                                    </Link>
                                </div>

                                <div className="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                    {navigationItems.map((item) => (
                                        <NavLink
                                            key={item.href}
                                            href={item.href}
                                            active={route().current(
                                                item.href.split("/")[1]
                                            )}
                                        >
                                            <div className="flex items-center gap-1.5">
                                                {item.icon && (
                                                    <item.icon className="h-4 w-4" />
                                                )}
                                                <span>{item.label}</span>
                                            </div>
                                        </NavLink>
                                    ))}
                                </div>
                            </div>

                            <div className="hidden sm:ms-6 sm:flex sm:items-center space-x-4">
                                {/* Notification Bell */}
                                <NotificationBell
                                    notifications={recentNotifications}
                                />

                                {/* User Dropdown */}
                                <div className="relative">
                                    <Dropdown>
                                        <Dropdown.Trigger>
                                            <span className="inline-flex rounded-md">
                                                <button
                                                    type="button"
                                                    className="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none"
                                                >
                                                    <div className="text-left">
                                                        <div>{user.name}</div>
                                                        <div className="text-xs text-gray-400 capitalize">
                                                            {user.role.replace(
                                                                "_",
                                                                " "
                                                            )}
                                                        </div>
                                                    </div>

                                                    <svg
                                                        className="-me-0.5 ms-2 h-4 w-4"
                                                        xmlns="http://www.w3.org/2000/svg"
                                                        viewBox="0 0 20 20"
                                                        fill="currentColor"
                                                    >
                                                        <path
                                                            fillRule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clipRule="evenodd"
                                                        />
                                                    </svg>
                                                </button>
                                            </span>
                                        </Dropdown.Trigger>

                                        <Dropdown.Content>
                                            <Dropdown.Link
                                                href={route("profile.edit")}
                                            >
                                                Profile
                                            </Dropdown.Link>
                                            <Dropdown.Link
                                                href={route("logout")}
                                                method="post"
                                                as="button"
                                            >
                                                Log Out
                                            </Dropdown.Link>
                                        </Dropdown.Content>
                                    </Dropdown>
                                </div>
                            </div>

                            <div className="-me-2 flex items-center sm:hidden">
                                <button
                                    onClick={() =>
                                        setShowingNavigationDropdown(
                                            (previousState) => !previousState
                                        )
                                    }
                                    className="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                    aria-expanded={showingNavigationDropdown}
                                    aria-controls="mobile-menu"
                                    aria-label={
                                        showingNavigationDropdown
                                            ? "Close navigation menu"
                                            : "Open navigation menu"
                                    }
                                >
                                    <svg
                                        className="h-6 w-6"
                                        stroke="currentColor"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        aria-hidden="true"
                                    >
                                        <path
                                            className={
                                                !showingNavigationDropdown
                                                    ? "inline-flex"
                                                    : "hidden"
                                            }
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M4 6h16M4 12h16M4 18h16"
                                        />
                                        <path
                                            className={
                                                showingNavigationDropdown
                                                    ? "inline-flex"
                                                    : "hidden"
                                            }
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth="2"
                                            d="M6 18L18 6M6 6l12 12"
                                        />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    {/* Mobile Navigation */}
                    <div
                        id="mobile-menu"
                        className={
                            (showingNavigationDropdown ? "block" : "hidden") +
                            " sm:hidden"
                        }
                        role="navigation"
                        aria-label="Mobile navigation"
                    >
                        <div className="space-y-1 pb-3 pt-2">
                            {navigationItems.map((item) => (
                                <ResponsiveNavLink
                                    key={item.href}
                                    href={item.href}
                                    active={route().current(
                                        item.href.split("/")[1]
                                    )}
                                >
                                    <div className="flex items-center gap-2">
                                        {item.icon && (
                                            <item.icon className="h-5 w-5" />
                                        )}
                                        <span>{item.label}</span>
                                    </div>
                                </ResponsiveNavLink>
                            ))}

                            {/* Mobile Notifications Link */}
                            <ResponsiveNavLink
                                href={route("notifications.index")}
                                active={route().current("notifications.index")}
                            >
                                <div className="flex items-center justify-between w-full">
                                    <span>Notifications</span>
                                    {unreadNotificationsCount > 0 && (
                                        <span className="inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                                            {unreadNotificationsCount > 99
                                                ? "99+"
                                                : unreadNotificationsCount}
                                        </span>
                                    )}
                                </div>
                            </ResponsiveNavLink>
                        </div>

                        <div className="border-t border-gray-200 pb-1 pt-4">
                            <div className="px-4">
                                <div className="text-base font-medium text-gray-800">
                                    {user.name}
                                </div>
                                <div className="text-sm font-medium text-gray-500">
                                    {user.email}
                                </div>
                                <div className="text-xs font-medium text-gray-400 capitalize mt-1">
                                    {user.role.replace("_", " ")}
                                </div>
                            </div>

                            <div className="mt-3 space-y-1">
                                <ResponsiveNavLink href={route("profile.edit")}>
                                    Profile
                                </ResponsiveNavLink>
                                <ResponsiveNavLink
                                    method="post"
                                    href={route("logout")}
                                    as="button"
                                >
                                    Log Out
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </nav>

                {header && (
                    <header className="bg-white shadow">
                        <div className="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                            {header}
                        </div>
                    </header>
                )}

                <main id="main-content" role="main" tabIndex="-1">
                    {children}
                </main>
            </div>
        </ErrorBoundary>
    );
}

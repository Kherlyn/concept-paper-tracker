import { useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router, useForm } from "@inertiajs/react";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import DangerButton from "@/Components/DangerButton";
import TextInput from "@/Components/TextInput";
import InputLabel from "@/Components/InputLabel";
import InputError from "@/Components/InputError";
import ValidationErrors from "@/Components/ValidationErrors";
import StatusBadge from "@/Components/StatusBadge";
import ConfirmationModal from "@/Components/ConfirmationModal";
import StageReassignment from "@/Components/StageReassignment";

export default function Users({ users, filters, roles }) {
    const [showCreateModal, setShowCreateModal] = useState(false);
    const [showEditModal, setShowEditModal] = useState(false);
    const [editingUser, setEditingUser] = useState(null);
    const [searchTerm, setSearchTerm] = useState(filters.search || "");
    const [roleFilter, setRoleFilter] = useState(filters.role || "");
    const [statusFilter, setStatusFilter] = useState(filters.is_active || "");
    const [schoolYearFilter, setSchoolYearFilter] = useState(
        filters.school_year || ""
    );
    const [showDeactivationWarning, setShowDeactivationWarning] =
        useState(false);
    const [showReassignmentModal, setShowReassignmentModal] = useState(false);
    const [userToToggle, setUserToToggle] = useState(null);
    const [affectedPapers, setAffectedPapers] = useState([]);
    const [isTogglingActivation, setIsTogglingActivation] = useState(false);
    const [sortField, setSortField] = useState("created_at");
    const [sortDirection, setSortDirection] = useState("desc");

    const createForm = useForm({
        name: "",
        email: "",
        password: "",
        role: "requisitioner",
        department: "",
        school_year: "",
        student_number: "",
        is_active: true,
    });

    const editForm = useForm({
        name: "",
        email: "",
        password: "",
        role: "",
        department: "",
        school_year: "",
        student_number: "",
        is_active: true,
    });

    const handleSearch = (e) => {
        e.preventDefault();
        router.get(
            "/admin/users",
            {
                search: searchTerm,
                role: roleFilter,
                is_active: statusFilter,
                school_year: schoolYearFilter,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    const handleCreateUser = (e) => {
        e.preventDefault();
        createForm.post("/admin/users", {
            onSuccess: () => {
                setShowCreateModal(false);
                createForm.reset();
            },
        });
    };

    const handleEditUser = (user) => {
        setEditingUser(user);
        editForm.setData({
            name: user.name,
            email: user.email,
            password: "",
            role: user.role,
            department: user.department || "",
            school_year: user.school_year || "",
            student_number: user.student_number || "",
            is_active: user.is_active,
        });
        setShowEditModal(true);
    };

    const handleUpdateUser = (e) => {
        e.preventDefault();
        editForm.put(`/admin/users/${editingUser.id}`, {
            onSuccess: () => {
                setShowEditModal(false);
                setEditingUser(null);
                editForm.reset();
            },
        });
    };

    const handleToggleActive = async (user) => {
        // If activating, just do it directly
        if (!user.is_active) {
            setIsTogglingActivation(true);
            try {
                const response = await fetch(
                    `/admin/users/${user.id}/toggle-activation`,
                    {
                        method: "PATCH",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document.querySelector(
                                'meta[name="csrf-token"]'
                            ).content,
                            Accept: "application/json",
                        },
                    }
                );

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                if (data.success) {
                    router.reload({ preserveScroll: true });
                } else {
                    alert(data.message || "Failed to activate user");
                }
            } catch (error) {
                console.error("Error toggling activation:", error);
                alert("Failed to toggle user activation. Please try again.");
            } finally {
                setIsTogglingActivation(false);
            }
            return;
        }

        // If deactivating, check for affected papers first
        setIsTogglingActivation(true);
        try {
            const response = await fetch(
                `/admin/users/${user.id}/assigned-stages`
            );
            const data = await response.json();

            if (data.affected_papers && data.affected_papers.length > 0) {
                // Show warning modal with affected papers
                setUserToToggle(user);
                setAffectedPapers(data.affected_papers);
                setShowDeactivationWarning(true);
            } else {
                // No affected papers, proceed with deactivation
                await performDeactivation(user);
            }
        } catch (error) {
            console.error("Error checking assigned stages:", error);
        } finally {
            setIsTogglingActivation(false);
        }
    };

    const performDeactivation = async (user) => {
        setIsTogglingActivation(true);
        try {
            const response = await fetch(
                `/admin/users/${user.id}/toggle-activation`,
                {
                    method: "PATCH",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector(
                            'meta[name="csrf-token"]'
                        ).content,
                        Accept: "application/json",
                    },
                }
            );

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();

            if (data.success) {
                setShowDeactivationWarning(false);
                setUserToToggle(null);
                setAffectedPapers([]);
                router.reload({ preserveScroll: true });
            } else {
                alert(data.message || "Failed to deactivate user");
            }
        } catch (error) {
            console.error("Error deactivating user:", error);
            alert("Failed to toggle user activation. Please try again.");
        } finally {
            setIsTogglingActivation(false);
        }
    };

    const handleSort = (field) => {
        if (sortField === field) {
            setSortDirection(sortDirection === "asc" ? "desc" : "asc");
        } else {
            setSortField(field);
            setSortDirection("asc");
        }

        router.get(
            "/admin/users",
            {
                search: searchTerm,
                role: roleFilter,
                is_active: statusFilter,
                school_year: schoolYearFilter,
                sort: field,
                direction:
                    sortField === field && sortDirection === "asc"
                        ? "desc"
                        : "asc",
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
    };

    const getRoleBadgeColor = (role) => {
        const colors = {
            admin: "bg-purple-100 text-purple-800",
            requisitioner: "bg-blue-100 text-blue-800",
            sps: "bg-green-100 text-green-800",
            vp_acad: "bg-indigo-100 text-indigo-800",
            auditor: "bg-yellow-100 text-yellow-800",
            accounting: "bg-pink-100 text-pink-800",
        };
        return colors[role] || "bg-gray-100 text-gray-800";
    };

    return (
        <AuthenticatedLayout>
            <Head title="User Management" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 border-b border-gray-200">
                            <div className="flex justify-between items-center mb-6">
                                <div>
                                    <h2 className="text-2xl font-semibold text-gray-900">
                                        User Management
                                    </h2>
                                    <p className="mt-1 text-sm text-gray-600">
                                        Manage system users and their roles
                                    </p>
                                </div>
                                <PrimaryButton
                                    onClick={() => setShowCreateModal(true)}
                                >
                                    <svg
                                        className="h-5 w-5 mr-2"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            strokeLinecap="round"
                                            strokeLinejoin="round"
                                            strokeWidth={2}
                                            d="M12 4v16m8-8H4"
                                        />
                                    </svg>
                                    Create User
                                </PrimaryButton>
                            </div>

                            {/* Search and Filters */}
                            <form
                                onSubmit={handleSearch}
                                className="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6"
                            >
                                <div>
                                    <TextInput
                                        type="text"
                                        placeholder="Search by name or email..."
                                        value={searchTerm}
                                        onChange={(e) =>
                                            setSearchTerm(e.target.value)
                                        }
                                        className="w-full"
                                    />
                                </div>
                                <div>
                                    <select
                                        value={roleFilter}
                                        onChange={(e) =>
                                            setRoleFilter(e.target.value)
                                        }
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Roles</option>
                                        {Object.entries(roles).map(
                                            ([key, label]) => (
                                                <option key={key} value={key}>
                                                    {label}
                                                </option>
                                            )
                                        )}
                                    </select>
                                </div>
                                <div>
                                    <select
                                        value={statusFilter}
                                        onChange={(e) =>
                                            setStatusFilter(e.target.value)
                                        }
                                        className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">All Status</option>
                                        <option value="1">Active</option>
                                        <option value="0">Inactive</option>
                                    </select>
                                </div>
                                <div>
                                    <TextInput
                                        type="text"
                                        placeholder="Filter by school year..."
                                        value={schoolYearFilter}
                                        onChange={(e) =>
                                            setSchoolYearFilter(e.target.value)
                                        }
                                        className="w-full"
                                    />
                                </div>
                                <div>
                                    <PrimaryButton
                                        type="submit"
                                        className="w-full"
                                    >
                                        Search
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>

                        {/* Users Table */}
                        <div className="overflow-x-auto">
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Role
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Department
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            School Year
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Student Number
                                        </th>
                                        <th
                                            className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100"
                                            onClick={() =>
                                                handleSort("is_active")
                                            }
                                        >
                                            <div className="flex items-center">
                                                Activation Status
                                                {sortField === "is_active" && (
                                                    <svg
                                                        className={`ml-1 h-4 w-4 transform ${
                                                            sortDirection ===
                                                            "desc"
                                                                ? "rotate-180"
                                                                : ""
                                                        }`}
                                                        fill="currentColor"
                                                        viewBox="0 0 20 20"
                                                    >
                                                        <path
                                                            fillRule="evenodd"
                                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                            clipRule="evenodd"
                                                        />
                                                    </svg>
                                                )}
                                            </div>
                                        </th>
                                        <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {users.data.length === 0 ? (
                                        <tr>
                                            <td
                                                colSpan="8"
                                                className="px-6 py-12 text-center text-sm text-gray-500"
                                            >
                                                No users found
                                            </td>
                                        </tr>
                                    ) : (
                                        users.data.map((user) => (
                                            <tr
                                                key={user.id}
                                                className="hover:bg-gray-50"
                                            >
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm font-medium text-gray-900">
                                                        {user.name}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <div className="text-sm text-gray-500">
                                                        {user.email}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span
                                                        className={`px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${getRoleBadgeColor(
                                                            user.role
                                                        )}`}
                                                    >
                                                        {roles[user.role]}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {user.department || "N/A"}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {user.school_year || "N/A"}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                    {user.student_number ||
                                                        "N/A"}
                                                </td>
                                                <td className="px-6 py-4">
                                                    <div className="space-y-2">
                                                        <div className="flex items-center">
                                                            <label className="inline-flex items-center cursor-pointer">
                                                                <input
                                                                    type="checkbox"
                                                                    checked={
                                                                        user.is_active
                                                                    }
                                                                    onChange={() =>
                                                                        handleToggleActive(
                                                                            user
                                                                        )
                                                                    }
                                                                    disabled={
                                                                        isTogglingActivation
                                                                    }
                                                                    className="sr-only peer"
                                                                />
                                                                <div className="relative w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                                                                <span className="ms-3 text-sm font-medium text-gray-900">
                                                                    {user.is_active ? (
                                                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                                            <svg
                                                                                className="mr-1 h-3 w-3"
                                                                                fill="currentColor"
                                                                                viewBox="0 0 20 20"
                                                                            >
                                                                                <path
                                                                                    fillRule="evenodd"
                                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                                                    clipRule="evenodd"
                                                                                />
                                                                            </svg>
                                                                            Active
                                                                        </span>
                                                                    ) : (
                                                                        <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                                            <svg
                                                                                className="mr-1 h-3 w-3"
                                                                                fill="currentColor"
                                                                                viewBox="0 0 20 20"
                                                                            >
                                                                                <path
                                                                                    fillRule="evenodd"
                                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                                                    clipRule="evenodd"
                                                                                />
                                                                            </svg>
                                                                            Inactive
                                                                        </span>
                                                                    )}
                                                                </span>
                                                            </label>
                                                        </div>
                                                        {!user.is_active &&
                                                            user.deactivated_at && (
                                                                <div className="text-xs text-gray-500">
                                                                    <div>
                                                                        Deactivated:{" "}
                                                                        {new Date(
                                                                            user.deactivated_at
                                                                        ).toLocaleDateString()}
                                                                    </div>
                                                                    {user.deactivated_by_user && (
                                                                        <div>
                                                                            By:{" "}
                                                                            {
                                                                                user
                                                                                    .deactivated_by_user
                                                                                    .name
                                                                            }
                                                                        </div>
                                                                    )}
                                                                </div>
                                                            )}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <button
                                                        onClick={() =>
                                                            handleEditUser(user)
                                                        }
                                                        className="text-indigo-600 hover:text-indigo-900"
                                                    >
                                                        Edit
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    )}
                                </tbody>
                            </table>
                        </div>

                        {/* Pagination */}
                        {users.links && users.links.length > 3 && (
                            <div className="px-6 py-4 border-t border-gray-200">
                                <div className="flex items-center justify-between">
                                    <div className="text-sm text-gray-700">
                                        Showing {users.from} to {users.to} of{" "}
                                        {users.total} results
                                    </div>
                                    <div className="flex space-x-2">
                                        {users.links.map((link, index) => (
                                            <button
                                                key={index}
                                                onClick={() =>
                                                    link.url &&
                                                    router.get(link.url)
                                                }
                                                disabled={!link.url}
                                                className={`px-3 py-1 rounded-md text-sm ${
                                                    link.active
                                                        ? "bg-indigo-600 text-white"
                                                        : "bg-white text-gray-700 hover:bg-gray-50"
                                                } ${
                                                    !link.url
                                                        ? "opacity-50 cursor-not-allowed"
                                                        : ""
                                                } border border-gray-300`}
                                                dangerouslySetInnerHTML={{
                                                    __html: link.label,
                                                }}
                                            />
                                        ))}
                                    </div>
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Create User Modal */}
            <Modal
                show={showCreateModal}
                onClose={() => setShowCreateModal(false)}
                maxWidth="2xl"
            >
                <form onSubmit={handleCreateUser} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 mb-4">
                        Create New User
                    </h2>

                    <ValidationErrors
                        errors={createForm.errors}
                        className="mb-4"
                    />

                    <div className="space-y-4">
                        <div>
                            <InputLabel htmlFor="create_name" value="Name" />
                            <TextInput
                                id="create_name"
                                type="text"
                                value={createForm.data.name}
                                onChange={(e) =>
                                    createForm.setData("name", e.target.value)
                                }
                                className="mt-1 block w-full"
                                required
                            />
                            <InputError
                                message={createForm.errors.name}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel htmlFor="create_email" value="Email" />
                            <TextInput
                                id="create_email"
                                type="email"
                                value={createForm.data.email}
                                onChange={(e) =>
                                    createForm.setData("email", e.target.value)
                                }
                                className="mt-1 block w-full"
                                required
                            />
                            <InputError
                                message={createForm.errors.email}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="create_password"
                                value="Password"
                            />
                            <TextInput
                                id="create_password"
                                type="password"
                                value={createForm.data.password}
                                onChange={(e) =>
                                    createForm.setData(
                                        "password",
                                        e.target.value
                                    )
                                }
                                className="mt-1 block w-full"
                                required
                            />
                            <InputError
                                message={createForm.errors.password}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel htmlFor="create_role" value="Role" />
                            <select
                                id="create_role"
                                value={createForm.data.role}
                                onChange={(e) =>
                                    createForm.setData("role", e.target.value)
                                }
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                {Object.entries(roles).map(([key, label]) => (
                                    <option key={key} value={key}>
                                        {label}
                                    </option>
                                ))}
                            </select>
                            <InputError
                                message={createForm.errors.role}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="create_department"
                                value="Department (Optional)"
                            />
                            <TextInput
                                id="create_department"
                                type="text"
                                value={createForm.data.department}
                                onChange={(e) =>
                                    createForm.setData(
                                        "department",
                                        e.target.value
                                    )
                                }
                                className="mt-1 block w-full"
                            />
                            <InputError
                                message={createForm.errors.department}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="create_school_year"
                                value="School Year (Optional)"
                            />
                            <TextInput
                                id="create_school_year"
                                type="text"
                                value={createForm.data.school_year}
                                onChange={(e) =>
                                    createForm.setData(
                                        "school_year",
                                        e.target.value
                                    )
                                }
                                className="mt-1 block w-full"
                                placeholder="e.g., 2024-2025 or 1st Year"
                            />
                            <InputError
                                message={createForm.errors.school_year}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="create_student_number"
                                value="Student Number (Optional)"
                            />
                            <TextInput
                                id="create_student_number"
                                type="text"
                                value={createForm.data.student_number}
                                onChange={(e) =>
                                    createForm.setData(
                                        "student_number",
                                        e.target.value
                                    )
                                }
                                className="mt-1 block w-full"
                                placeholder="e.g., 2024-00001"
                            />
                            <InputError
                                message={createForm.errors.student_number}
                                className="mt-2"
                            />
                        </div>

                        <div className="flex items-center">
                            <input
                                id="create_is_active"
                                type="checkbox"
                                checked={createForm.data.is_active}
                                onChange={(e) =>
                                    createForm.setData(
                                        "is_active",
                                        e.target.checked
                                    )
                                }
                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <InputLabel
                                htmlFor="create_is_active"
                                value="Active"
                                className="ml-2"
                            />
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end space-x-3">
                        <SecondaryButton
                            type="button"
                            onClick={() => setShowCreateModal(false)}
                        >
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            disabled={createForm.processing}
                        >
                            {createForm.processing
                                ? "Creating..."
                                : "Create User"}
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>

            {/* Edit User Modal */}
            <Modal
                show={showEditModal}
                onClose={() => setShowEditModal(false)}
                maxWidth="2xl"
            >
                <form onSubmit={handleUpdateUser} className="p-6">
                    <h2 className="text-lg font-medium text-gray-900 mb-4">
                        Edit User
                    </h2>

                    <ValidationErrors
                        errors={editForm.errors}
                        className="mb-4"
                    />

                    <div className="space-y-4">
                        <div>
                            <InputLabel htmlFor="edit_name" value="Name" />
                            <TextInput
                                id="edit_name"
                                type="text"
                                value={editForm.data.name}
                                onChange={(e) =>
                                    editForm.setData("name", e.target.value)
                                }
                                className="mt-1 block w-full"
                                required
                            />
                            <InputError
                                message={editForm.errors.name}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel htmlFor="edit_email" value="Email" />
                            <TextInput
                                id="edit_email"
                                type="email"
                                value={editForm.data.email}
                                onChange={(e) =>
                                    editForm.setData("email", e.target.value)
                                }
                                className="mt-1 block w-full"
                                required
                            />
                            <InputError
                                message={editForm.errors.email}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="edit_password"
                                value="Password (Leave blank to keep current)"
                            />
                            <TextInput
                                id="edit_password"
                                type="password"
                                value={editForm.data.password}
                                onChange={(e) =>
                                    editForm.setData("password", e.target.value)
                                }
                                className="mt-1 block w-full"
                            />
                            <InputError
                                message={editForm.errors.password}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel htmlFor="edit_role" value="Role" />
                            <select
                                id="edit_role"
                                value={editForm.data.role}
                                onChange={(e) =>
                                    editForm.setData("role", e.target.value)
                                }
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                            >
                                {Object.entries(roles).map(([key, label]) => (
                                    <option key={key} value={key}>
                                        {label}
                                    </option>
                                ))}
                            </select>
                            <InputError
                                message={editForm.errors.role}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="edit_department"
                                value="Department (Optional)"
                            />
                            <TextInput
                                id="edit_department"
                                type="text"
                                value={editForm.data.department}
                                onChange={(e) =>
                                    editForm.setData(
                                        "department",
                                        e.target.value
                                    )
                                }
                                className="mt-1 block w-full"
                            />
                            <InputError
                                message={editForm.errors.department}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="edit_school_year"
                                value="School Year (Optional)"
                            />
                            <TextInput
                                id="edit_school_year"
                                type="text"
                                value={editForm.data.school_year}
                                onChange={(e) =>
                                    editForm.setData(
                                        "school_year",
                                        e.target.value
                                    )
                                }
                                className="mt-1 block w-full"
                                placeholder="e.g., 2024-2025 or 1st Year"
                            />
                            <InputError
                                message={editForm.errors.school_year}
                                className="mt-2"
                            />
                        </div>

                        <div>
                            <InputLabel
                                htmlFor="edit_student_number"
                                value="Student Number (Optional)"
                            />
                            <TextInput
                                id="edit_student_number"
                                type="text"
                                value={editForm.data.student_number}
                                onChange={(e) =>
                                    editForm.setData(
                                        "student_number",
                                        e.target.value
                                    )
                                }
                                className="mt-1 block w-full"
                                placeholder="e.g., 2024-00001"
                            />
                            <InputError
                                message={editForm.errors.student_number}
                                className="mt-2"
                            />
                        </div>

                        <div className="flex items-center">
                            <input
                                id="edit_is_active"
                                type="checkbox"
                                checked={editForm.data.is_active}
                                onChange={(e) =>
                                    editForm.setData(
                                        "is_active",
                                        e.target.checked
                                    )
                                }
                                className="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            />
                            <InputLabel
                                htmlFor="edit_is_active"
                                value="Active"
                                className="ml-2"
                            />
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end space-x-3">
                        <SecondaryButton
                            type="button"
                            onClick={() => setShowEditModal(false)}
                        >
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            type="submit"
                            disabled={editForm.processing}
                        >
                            {editForm.processing
                                ? "Updating..."
                                : "Update User"}
                        </PrimaryButton>
                    </div>
                </form>
            </Modal>

            {/* Deactivation Warning Modal */}
            <Modal
                show={showDeactivationWarning}
                onClose={() => {
                    setShowDeactivationWarning(false);
                    setUserToToggle(null);
                    setAffectedPapers([]);
                }}
                maxWidth="3xl"
            >
                <div className="p-6">
                    <div className="flex items-start">
                        <div className="flex-shrink-0">
                            <svg
                                className="h-6 w-6 text-yellow-600"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    strokeLinecap="round"
                                    strokeLinejoin="round"
                                    strokeWidth={2}
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                                />
                            </svg>
                        </div>
                        <div className="ml-3 flex-1">
                            <h3 className="text-lg font-medium text-gray-900">
                                Warning: User Has Pending Workflow Stages
                            </h3>
                            <div className="mt-2 text-sm text-gray-600">
                                <p>
                                    This user has {affectedPapers.length}{" "}
                                    concept paper(s) with pending workflow
                                    stages. You must reassign these stages
                                    before deactivating the user.
                                </p>
                            </div>

                            <div className="mt-4 max-h-96 overflow-y-auto">
                                <h4 className="text-sm font-medium text-gray-900 mb-2">
                                    Affected Concept Papers:
                                </h4>
                                <div className="space-y-3">
                                    {affectedPapers.map((paper) => (
                                        <div
                                            key={paper.id}
                                            className="bg-gray-50 rounded-lg p-4 border border-gray-200"
                                        >
                                            <div className="flex justify-between items-start">
                                                <div>
                                                    <h5 className="font-medium text-gray-900">
                                                        {paper.title}
                                                    </h5>
                                                    <p className="text-sm text-gray-600">
                                                        Tracking:{" "}
                                                        {paper.tracking_number}
                                                    </p>
                                                    <p className="text-sm text-gray-600">
                                                        Requisitioner:{" "}
                                                        {paper.requisitioner}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="mt-2">
                                                <p className="text-xs font-medium text-gray-700 mb-1">
                                                    Pending Stages:
                                                </p>
                                                <div className="space-y-1">
                                                    {paper.stages.map(
                                                        (stage) => (
                                                            <div
                                                                key={stage.id}
                                                                className="text-xs text-gray-600 flex items-center"
                                                            >
                                                                <span className="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                    {
                                                                        stage.stage_name
                                                                    }
                                                                </span>
                                                                <span className="ml-2">
                                                                    Status:{" "}
                                                                    {
                                                                        stage.status
                                                                    }
                                                                </span>
                                                            </div>
                                                        )
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            <div className="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
                                <div className="flex">
                                    <div className="flex-shrink-0">
                                        <svg
                                            className="h-5 w-5 text-blue-400"
                                            fill="currentColor"
                                            viewBox="0 0 20 20"
                                        >
                                            <path
                                                fillRule="evenodd"
                                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                                clipRule="evenodd"
                                            />
                                        </svg>
                                    </div>
                                    <div className="ml-3">
                                        <p className="text-sm text-blue-700">
                                            Please reassign these stages to
                                            other active users before
                                            deactivating this user.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div className="mt-6 flex justify-end space-x-3">
                        <SecondaryButton
                            onClick={() => {
                                setShowDeactivationWarning(false);
                                setUserToToggle(null);
                                setAffectedPapers([]);
                            }}
                        >
                            Cancel
                        </SecondaryButton>
                        <PrimaryButton
                            onClick={() => {
                                setShowDeactivationWarning(false);
                                setShowReassignmentModal(true);
                            }}
                        >
                            Reassign Stages
                        </PrimaryButton>
                        <DangerButton
                            onClick={() => {
                                if (userToToggle) {
                                    performDeactivation(userToToggle);
                                }
                            }}
                            disabled={isTogglingActivation}
                        >
                            {isTogglingActivation
                                ? "Deactivating..."
                                : "Deactivate Anyway"}
                        </DangerButton>
                    </div>
                </div>
            </Modal>

            {/* Stage Reassignment Modal */}
            <StageReassignment
                isOpen={showReassignmentModal}
                onClose={() => {
                    setShowReassignmentModal(false);
                    setUserToToggle(null);
                    setAffectedPapers([]);
                }}
                affectedPapers={affectedPapers}
                userToDeactivate={userToToggle}
                onReassignmentComplete={() => {
                    // After successful reassignment, proceed with deactivation
                    if (userToToggle) {
                        performDeactivation(userToToggle);
                    }
                }}
            />
        </AuthenticatedLayout>
    );
}

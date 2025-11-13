import { useState, useMemo } from "react";

export default function DataTable({
    columns,
    data,
    sortable = true,
    filterable = true,
    pagination = true,
    itemsPerPage = 10,
    emptyMessage = "No data available",
}) {
    const [sortConfig, setSortConfig] = useState({
        key: null,
        direction: "asc",
    });
    const [filterText, setFilterText] = useState("");
    const [currentPage, setCurrentPage] = useState(1);

    // Filter data
    const filteredData = useMemo(() => {
        if (!filterable || !filterText) return data;

        return data.filter((row) => {
            return columns.some((column) => {
                const value = column.accessor
                    ? column.accessor(row)
                    : row[column.key];
                return String(value)
                    .toLowerCase()
                    .includes(filterText.toLowerCase());
            });
        });
    }, [data, filterText, columns, filterable]);

    // Sort data
    const sortedData = useMemo(() => {
        if (!sortable || !sortConfig.key) return filteredData;

        const sorted = [...filteredData].sort((a, b) => {
            const column = columns.find((col) => col.key === sortConfig.key);
            const aValue = column.accessor
                ? column.accessor(a)
                : a[sortConfig.key];
            const bValue = column.accessor
                ? column.accessor(b)
                : b[sortConfig.key];

            if (aValue === null || aValue === undefined) return 1;
            if (bValue === null || bValue === undefined) return -1;

            if (typeof aValue === "string") {
                return sortConfig.direction === "asc"
                    ? aValue.localeCompare(bValue)
                    : bValue.localeCompare(aValue);
            }

            if (sortConfig.direction === "asc") {
                return aValue > bValue ? 1 : -1;
            } else {
                return aValue < bValue ? 1 : -1;
            }
        });

        return sorted;
    }, [filteredData, sortConfig, columns, sortable]);

    // Paginate data
    const paginatedData = useMemo(() => {
        if (!pagination) return sortedData;

        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = startIndex + itemsPerPage;
        return sortedData.slice(startIndex, endIndex);
    }, [sortedData, currentPage, itemsPerPage, pagination]);

    const totalPages = Math.ceil(sortedData.length / itemsPerPage);

    const handleSort = (key) => {
        if (!sortable) return;

        setSortConfig((prevConfig) => ({
            key,
            direction:
                prevConfig.key === key && prevConfig.direction === "asc"
                    ? "desc"
                    : "asc",
        }));
    };

    const handlePageChange = (page) => {
        setCurrentPage(page);
    };

    const getSortIcon = (columnKey) => {
        if (sortConfig.key !== columnKey) {
            return (
                <svg
                    className="h-4 w-4 text-gray-400"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path
                        strokeLinecap="round"
                        strokeLinejoin="round"
                        strokeWidth="2"
                        d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"
                    />
                </svg>
            );
        }

        return sortConfig.direction === "asc" ? (
            <svg
                className="h-4 w-4 text-indigo-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M5 15l7-7 7 7"
                />
            </svg>
        ) : (
            <svg
                className="h-4 w-4 text-indigo-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    strokeWidth="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        );
    };

    return (
        <div className="space-y-4">
            {filterable && (
                <div className="flex items-center">
                    <div className="relative flex-1 max-w-md">
                        <label htmlFor="table-search" className="sr-only">
                            Search table
                        </label>
                        <input
                            id="table-search"
                            type="text"
                            placeholder="Search..."
                            value={filterText}
                            onChange={(e) => {
                                setFilterText(e.target.value);
                                setCurrentPage(1);
                            }}
                            className="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 pl-10"
                            aria-label="Search table data"
                        />
                        <svg
                            className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-gray-400 pointer-events-none"
                            fill="none"
                            stroke="currentColor"
                            viewBox="0 0 24 24"
                            aria-hidden="true"
                        >
                            <path
                                strokeLinecap="round"
                                strokeLinejoin="round"
                                strokeWidth="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                            />
                        </svg>
                    </div>
                </div>
            )}

            {/* Desktop Table View */}
            <div className="hidden md:block overflow-x-auto shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table className="min-w-full divide-y divide-gray-300">
                    <thead className="bg-gray-50">
                        <tr>
                            {columns.map((column) => (
                                <th
                                    key={column.key}
                                    scope="col"
                                    className={`px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider ${
                                        sortable && column.sortable !== false
                                            ? "cursor-pointer hover:bg-gray-100 select-none focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            : ""
                                    }`}
                                    onClick={() =>
                                        sortable &&
                                        column.sortable !== false &&
                                        handleSort(column.key)
                                    }
                                    onKeyDown={(e) => {
                                        if (
                                            sortable &&
                                            column.sortable !== false &&
                                            (e.key === "Enter" || e.key === " ")
                                        ) {
                                            e.preventDefault();
                                            handleSort(column.key);
                                        }
                                    }}
                                    tabIndex={
                                        sortable && column.sortable !== false
                                            ? 0
                                            : undefined
                                    }
                                    role={
                                        sortable && column.sortable !== false
                                            ? "button"
                                            : undefined
                                    }
                                    aria-sort={
                                        sortConfig.key === column.key
                                            ? sortConfig.direction === "asc"
                                                ? "ascending"
                                                : "descending"
                                            : undefined
                                    }
                                >
                                    <div className="flex items-center space-x-1">
                                        <span>{column.label}</span>
                                        {sortable &&
                                            column.sortable !== false &&
                                            getSortIcon(column.key)}
                                    </div>
                                </th>
                            ))}
                        </tr>
                    </thead>
                    <tbody className="bg-white divide-y divide-gray-200">
                        {paginatedData.length === 0 ? (
                            <tr>
                                <td
                                    colSpan={columns.length}
                                    className="px-6 py-12 text-center text-sm text-gray-500"
                                >
                                    {emptyMessage}
                                </td>
                            </tr>
                        ) : (
                            paginatedData.map((row, rowIndex) => (
                                <tr key={rowIndex} className="hover:bg-gray-50">
                                    {columns.map((column) => (
                                        <td
                                            key={column.key}
                                            className="px-6 py-4 whitespace-nowrap text-sm text-gray-900"
                                        >
                                            {column.render
                                                ? column.render(row)
                                                : column.accessor
                                                ? column.accessor(row)
                                                : row[column.key]}
                                        </td>
                                    ))}
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>

            {/* Mobile Card View */}
            <div className="md:hidden space-y-4">
                {paginatedData.length === 0 ? (
                    <div className="bg-white shadow rounded-lg p-6 text-center text-sm text-gray-500">
                        {emptyMessage}
                    </div>
                ) : (
                    paginatedData.map((row, rowIndex) => (
                        <div
                            key={rowIndex}
                            className="bg-white shadow rounded-lg p-4 space-y-3"
                        >
                            {columns.map((column) => (
                                <div key={column.key} className="flex flex-col">
                                    <span className="text-xs font-medium text-gray-500 uppercase">
                                        {column.label}
                                    </span>
                                    <span className="mt-1 text-sm text-gray-900">
                                        {column.render
                                            ? column.render(row)
                                            : column.accessor
                                            ? column.accessor(row)
                                            : row[column.key]}
                                    </span>
                                </div>
                            ))}
                        </div>
                    ))
                )}
            </div>

            {pagination && totalPages > 1 && (
                <div className="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6 rounded-lg">
                    <div className="flex justify-between w-full sm:hidden">
                        <button
                            onClick={() => handlePageChange(currentPage - 1)}
                            disabled={currentPage === 1}
                            className="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            aria-label="Go to previous page"
                        >
                            Previous
                        </button>
                        <span
                            className="text-sm text-gray-700 flex items-center"
                            aria-live="polite"
                        >
                            Page {currentPage} of {totalPages}
                        </span>
                        <button
                            onClick={() => handlePageChange(currentPage + 1)}
                            disabled={currentPage === totalPages}
                            className="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            aria-label="Go to next page"
                        >
                            Next
                        </button>
                    </div>
                    <div className="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
                        <div>
                            <p
                                className="text-sm text-gray-700"
                                aria-live="polite"
                            >
                                Showing{" "}
                                <span className="font-medium">
                                    {(currentPage - 1) * itemsPerPage + 1}
                                </span>{" "}
                                to{" "}
                                <span className="font-medium">
                                    {Math.min(
                                        currentPage * itemsPerPage,
                                        sortedData.length
                                    )}
                                </span>{" "}
                                of{" "}
                                <span className="font-medium">
                                    {sortedData.length}
                                </span>{" "}
                                results
                            </p>
                        </div>
                        <div>
                            <nav
                                className="relative z-0 inline-flex rounded-md shadow-sm -space-x-px"
                                aria-label="Pagination"
                            >
                                <button
                                    onClick={() =>
                                        handlePageChange(currentPage - 1)
                                    }
                                    disabled={currentPage === 1}
                                    className="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10"
                                    aria-label="Go to previous page"
                                >
                                    <span className="sr-only">Previous</span>
                                    <svg
                                        className="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fillRule="evenodd"
                                            d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </button>

                                {[...Array(totalPages)].map((_, index) => {
                                    const page = index + 1;
                                    // Show first page, last page, current page, and pages around current
                                    if (
                                        page === 1 ||
                                        page === totalPages ||
                                        (page >= currentPage - 1 &&
                                            page <= currentPage + 1)
                                    ) {
                                        return (
                                            <button
                                                key={page}
                                                onClick={() =>
                                                    handlePageChange(page)
                                                }
                                                className={`relative inline-flex items-center px-4 py-2 border text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10 ${
                                                    currentPage === page
                                                        ? "z-10 bg-indigo-50 border-indigo-500 text-indigo-600"
                                                        : "bg-white border-gray-300 text-gray-500 hover:bg-gray-50"
                                                }`}
                                                aria-label={`Go to page ${page}`}
                                                aria-current={
                                                    currentPage === page
                                                        ? "page"
                                                        : undefined
                                                }
                                            >
                                                {page}
                                            </button>
                                        );
                                    } else if (
                                        page === currentPage - 2 ||
                                        page === currentPage + 2
                                    ) {
                                        return (
                                            <span
                                                key={page}
                                                className="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700"
                                                aria-hidden="true"
                                            >
                                                ...
                                            </span>
                                        );
                                    }
                                    return null;
                                })}

                                <button
                                    onClick={() =>
                                        handlePageChange(currentPage + 1)
                                    }
                                    disabled={currentPage === totalPages}
                                    className="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 disabled:opacity-50 disabled:cursor-not-allowed focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:z-10"
                                    aria-label="Go to next page"
                                >
                                    <span className="sr-only">Next</span>
                                    <svg
                                        className="h-5 w-5"
                                        fill="currentColor"
                                        viewBox="0 0 20 20"
                                        aria-hidden="true"
                                    >
                                        <path
                                            fillRule="evenodd"
                                            d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                                            clipRule="evenodd"
                                        />
                                    </svg>
                                </button>
                            </nav>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}

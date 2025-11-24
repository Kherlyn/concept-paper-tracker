import { render, screen, waitFor } from "@testing-library/react";
import { describe, it, expect, vi, beforeEach } from "vitest";
import DiscrepancySummary from "../DiscrepancySummary";
import axios from "axios";

// Mock axios
vi.mock("axios");

// Mock the route helper
global.route = vi.fn(
    (name, params) => `/concept-papers/${params}/discrepancies`
);

describe("DiscrepancySummary", () => {
    beforeEach(() => {
        vi.clearAllMocks();
    });

    it("renders loading state initially", () => {
        axios.get.mockImplementation(() => new Promise(() => {}));

        render(<DiscrepancySummary conceptPaperId={1} />);

        expect(screen.getByText(/Loading discrepancies/i)).toBeInTheDocument();
    });

    it("displays no discrepancies message when list is empty", async () => {
        axios.get.mockResolvedValue({
            data: {
                success: true,
                data: [],
            },
        });

        render(<DiscrepancySummary conceptPaperId={1} />);

        await waitFor(() => {
            expect(
                screen.getByText(/No discrepancies found/i)
            ).toBeInTheDocument();
        });
    });

    it("displays list of discrepancies", async () => {
        const mockDiscrepancies = [
            {
                id: 1,
                attachment_id: 1,
                page_number: 2,
                comment: "Budget calculation error",
                created_at: "2024-01-15T10:30:00Z",
                user: {
                    name: "John Doe",
                },
                attachment: {
                    file_name: "concept_paper.pdf",
                },
            },
            {
                id: 2,
                attachment_id: 1,
                page_number: 5,
                comment: "Missing signature",
                created_at: "2024-01-16T14:20:00Z",
                user: {
                    name: "Jane Smith",
                },
                attachment: {
                    file_name: "concept_paper.pdf",
                },
            },
        ];

        axios.get.mockResolvedValue({
            data: {
                success: true,
                data: mockDiscrepancies,
            },
        });

        render(<DiscrepancySummary conceptPaperId={1} />);

        await waitFor(() => {
            expect(
                screen.getByText(/Budget calculation error/i)
            ).toBeInTheDocument();
            expect(screen.getByText(/Missing signature/i)).toBeInTheDocument();
            expect(screen.getByText(/John Doe/i)).toBeInTheDocument();
            expect(screen.getByText(/Jane Smith/i)).toBeInTheDocument();
        });
    });

    it("displays discrepancy count badge", async () => {
        const mockDiscrepancies = [
            {
                id: 1,
                attachment_id: 1,
                page_number: 2,
                comment: "Test issue",
                created_at: "2024-01-15T10:30:00Z",
                user: { name: "Test User" },
                attachment: { file_name: "test.pdf" },
            },
        ];

        axios.get.mockResolvedValue({
            data: {
                success: true,
                data: mockDiscrepancies,
            },
        });

        render(<DiscrepancySummary conceptPaperId={1} />);

        await waitFor(() => {
            expect(screen.getByText(/1 issue/i)).toBeInTheDocument();
        });
    });

    it("shows page number for each discrepancy", async () => {
        const mockDiscrepancies = [
            {
                id: 1,
                attachment_id: 1,
                page_number: 3,
                comment: "Test issue",
                created_at: "2024-01-15T10:30:00Z",
                user: { name: "Test User" },
                attachment: { file_name: "test.pdf" },
            },
        ];

        axios.get.mockResolvedValue({
            data: {
                success: true,
                data: mockDiscrepancies,
            },
        });

        render(<DiscrepancySummary conceptPaperId={1} />);

        await waitFor(() => {
            expect(screen.getByText(/Page 3/i)).toBeInTheDocument();
        });
    });

    it("displays document name for each discrepancy", async () => {
        const mockDiscrepancies = [
            {
                id: 1,
                attachment_id: 1,
                page_number: 2,
                comment: "Test issue",
                created_at: "2024-01-15T10:30:00Z",
                user: { name: "Test User" },
                attachment: { file_name: "budget_report.pdf" },
            },
        ];

        axios.get.mockResolvedValue({
            data: {
                success: true,
                data: mockDiscrepancies,
            },
        });

        render(<DiscrepancySummary conceptPaperId={1} />);

        await waitFor(() => {
            expect(screen.getByText(/budget_report.pdf/i)).toBeInTheDocument();
        });
    });

    it("handles error state gracefully", async () => {
        axios.get.mockRejectedValue({
            response: {
                data: {
                    message: "Failed to load discrepancies",
                },
            },
        });

        render(<DiscrepancySummary conceptPaperId={1} />);

        await waitFor(() => {
            expect(
                screen.getByText(/Failed to load discrepancies/i)
            ).toBeInTheDocument();
        });
    });

    it("provides navigation button for each discrepancy", async () => {
        const mockDiscrepancies = [
            {
                id: 1,
                attachment_id: 1,
                page_number: 2,
                comment: "Test issue",
                created_at: "2024-01-15T10:30:00Z",
                user: { name: "Test User" },
                attachment: { file_name: "test.pdf" },
            },
        ];

        axios.get.mockResolvedValue({
            data: {
                success: true,
                data: mockDiscrepancies,
            },
        });

        const mockNavigate = vi.fn();
        render(
            <DiscrepancySummary
                conceptPaperId={1}
                onNavigateToDiscrepancy={mockNavigate}
            />
        );

        await waitFor(() => {
            expect(screen.getByText(/View in Document/i)).toBeInTheDocument();
        });
    });

    it("displays user who created discrepancy", async () => {
        const mockDiscrepancies = [
            {
                id: 1,
                attachment_id: 1,
                page_number: 2,
                comment: "Test issue",
                created_at: "2024-01-15T10:30:00Z",
                user: { name: "Alice Johnson" },
                attachment: { file_name: "test.pdf" },
            },
        ];

        axios.get.mockResolvedValue({
            data: {
                success: true,
                data: mockDiscrepancies,
            },
        });

        render(<DiscrepancySummary conceptPaperId={1} />);

        await waitFor(() => {
            expect(screen.getByText(/Reported by/i)).toBeInTheDocument();
            expect(screen.getByText(/Alice Johnson/i)).toBeInTheDocument();
        });
    });

    it("formats timestamp correctly", async () => {
        const mockDiscrepancies = [
            {
                id: 1,
                attachment_id: 1,
                page_number: 2,
                comment: "Test issue",
                created_at: "2024-01-15T10:30:00Z",
                user: { name: "Test User" },
                attachment: { file_name: "test.pdf" },
            },
        ];

        axios.get.mockResolvedValue({
            data: {
                success: true,
                data: mockDiscrepancies,
            },
        });

        render(<DiscrepancySummary conceptPaperId={1} />);

        await waitFor(() => {
            // The date should be formatted (exact format depends on locale)
            expect(screen.getByText(/Jan/i)).toBeInTheDocument();
        });
    });
});

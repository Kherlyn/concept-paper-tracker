<?php

namespace App\Http\Controllers;

use App\Models\ErrorReport;
use App\Models\User;
use App\Notifications\ErrorReportSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Inertia\Inertia;

class ErrorReportController extends Controller
{
    /**
     * Store a newly created error report.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:2000',
            'page_url' => 'nullable|string|max:500',
            'steps_to_reproduce' => 'nullable|string|max:2000',
            'error_message' => 'nullable|string|max:1000',
            'browser_info' => 'nullable|string|max:500',
        ]);

        $errorReport = ErrorReport::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'page_url' => $validated['page_url'] ?? $request->headers->get('referer'),
            'steps_to_reproduce' => $validated['steps_to_reproduce'] ?? null,
            'error_message' => $validated['error_message'] ?? null,
            'browser_info' => $validated['browser_info'] ?? $request->userAgent(),
            'status' => ErrorReport::STATUS_PENDING,
        ]);

        // Notify all admins
        $admins = User::where('role', 'admin')->where('is_active', true)->get();
        Notification::send($admins, new ErrorReportSubmittedNotification($errorReport));

        return back()->with('success', 'Your error report has been submitted. Thank you for helping us improve!');
    }

    /**
     * Display a listing of error reports (admin only).
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = ErrorReport::with(['user:id,name,email,role'])
            ->orderBy('created_at', 'desc');
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->paginate(15);
        
        $stats = [
            'pending' => ErrorReport::where('status', ErrorReport::STATUS_PENDING)->count(),
            'in_progress' => ErrorReport::where('status', ErrorReport::STATUS_IN_PROGRESS)->count(),
            'resolved' => ErrorReport::where('status', ErrorReport::STATUS_RESOLVED)->count(),
            'total' => ErrorReport::count(),
        ];

        return Inertia::render('Admin/ErrorReports', [
            'reports' => $reports,
            'stats' => $stats,
            'currentFilter' => $status,
        ]);
    }

    /**
     * Update the status of an error report (admin only).
     */
    public function update(Request $request, ErrorReport $errorReport)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved,dismissed',
            'admin_notes' => 'nullable|string|max:2000',
        ]);

        $errorReport->update([
            'status' => $validated['status'],
            'admin_notes' => $validated['admin_notes'] ?? $errorReport->admin_notes,
            'resolved_at' => in_array($validated['status'], ['resolved', 'dismissed']) ? now() : null,
            'resolved_by' => in_array($validated['status'], ['resolved', 'dismissed']) ? Auth::id() : null,
        ]);

        return back()->with('success', 'Error report status updated successfully.');
    }

    /**
     * Delete an error report (admin only).
     */
    public function destroy(ErrorReport $errorReport)
    {
        $errorReport->delete();

        return back()->with('success', 'Error report deleted successfully.');
    }
}

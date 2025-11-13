<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ConceptPaper;
use App\Services\ReportService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class AdminController extends Controller
{
  protected ReportService $reportService;

  public function __construct(ReportService $reportService)
  {
    $this->reportService = $reportService;
  }

  /**
   * Display a listing of all users with filtering.
   *
   * @param Request $request
   * @return Response
   */
  public function users(Request $request): Response
  {
    $query = User::query();

    // Filter by role if provided
    if ($request->has('role') && $request->role !== '') {
      $query->where('role', $request->role);
    }

    // Filter by department if provided
    if ($request->has('department') && $request->department !== '') {
      $query->where('department', 'like', '%' . $request->department . '%');
    }

    // Filter by school year if provided
    if ($request->has('school_year') && $request->school_year !== '') {
      $query->where('school_year', 'like', '%' . $request->school_year . '%');
    }

    // Filter by active status if provided
    if ($request->has('is_active') && $request->is_active !== '') {
      $query->where('is_active', $request->is_active === '1' || $request->is_active === 'true');
    }

    // Search by name or email
    if ($request->has('search') && $request->search !== '') {
      $query->where(function ($q) use ($request) {
        $q->where('name', 'like', '%' . $request->search . '%')
          ->orWhere('email', 'like', '%' . $request->search . '%');
      });
    }

    // Order by created_at descending by default
    $users = $query->orderBy('created_at', 'desc')->paginate(15);

    return Inertia::render('Admin/Users', [
      'users' => $users,
      'filters' => [
        'role' => $request->role ?? '',
        'department' => $request->department ?? '',
        'school_year' => $request->school_year ?? '',
        'is_active' => $request->is_active ?? '',
        'search' => $request->search ?? '',
      ],
      'roles' => [
        'requisitioner' => 'Requisitioner',
        'sps' => 'SPS',
        'vp_acad' => 'VP Acad',
        'auditor' => 'Auditor',
        'accounting' => 'Accounting',
        'admin' => 'Admin',
      ],
    ]);
  }

  /**
   * Store a newly created user with role assignment.
   *
   * @param StoreUserRequest $request
   * @return \Illuminate\Http\RedirectResponse
   */
  public function store(StoreUserRequest $request)
  {
    $validated = $request->validated();
    $validated['password'] = Hash::make($validated['password']);

    User::create($validated);

    return redirect()->route('admin.users')
      ->with('success', 'User created successfully.');
  }

  /**
   * Update the specified user's details and role.
   *
   * @param UpdateUserRequest $request
   * @param User $user
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(UpdateUserRequest $request, User $user)
  {
    $validated = $request->validated();

    // Only update password if provided
    if (!empty($validated['password'])) {
      $validated['password'] = Hash::make($validated['password']);
    } else {
      unset($validated['password']);
    }

    $user->update($validated);

    return redirect()->route('admin.users')
      ->with('success', 'User updated successfully.');
  }

  /**
   * Toggle the active status of a user.
   *
   * @param User $user
   * @return \Illuminate\Http\RedirectResponse
   */
  public function toggleActive(User $user)
  {
    $user->update([
      'is_active' => !$user->is_active,
    ]);

    $status = $user->is_active ? 'activated' : 'deactivated';

    return redirect()->route('admin.users')
      ->with('success', "User {$status} successfully.");
  }

  /**
   * Display the reports interface.
   *
   * @param Request $request
   * @return Response
   */
  public function reports(Request $request): Response
  {
    // Get processing statistics
    $statistics = $this->reportService->getProcessingStatistics();

    // Get stage averages
    $stageAverages = $this->reportService->getStageAverages();

    return Inertia::render('Admin/Reports', [
      'statistics' => $statistics,
      'stage_averages' => $stageAverages,
    ]);
  }

  /**
   * Download CSV export of concept papers.
   *
   * @param Request $request
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function downloadCsv(Request $request)
  {
    $filters = $request->validate([
      'status' => ['nullable', Rule::in(['pending', 'in_progress', 'completed', 'returned'])],
      'date_from' => ['nullable', 'date'],
      'date_to' => ['nullable', 'date'],
    ]);

    $filePath = $this->reportService->generateCsvExport($filters);

    return response()->download($filePath, 'concept-papers-' . now()->format('Y-m-d') . '.csv')
      ->deleteFileAfterSend(true);
  }

  /**
   * Download PDF report for a specific concept paper.
   *
   * @param ConceptPaper $conceptPaper
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   */
  public function downloadPdf(ConceptPaper $conceptPaper)
  {
    $filePath = $this->reportService->generatePdfReport($conceptPaper);

    return response()->download($filePath, 'concept-paper-' . $conceptPaper->tracking_number . '.pdf')
      ->deleteFileAfterSend(true);
  }
}

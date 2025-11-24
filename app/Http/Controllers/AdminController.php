<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ConceptPaper;
use App\Models\WorkflowStage;
use App\Services\ReportService;
use App\Services\WorkflowService;
use App\Services\NotificationService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
  use AuthorizesRequests;
  protected ReportService $reportService;
  protected WorkflowService $workflowService;
  protected NotificationService $notificationService;

  public function __construct(
    ReportService $reportService,
    WorkflowService $workflowService,
    NotificationService $notificationService
  ) {
    $this->reportService = $reportService;
    $this->workflowService = $workflowService;
    $this->notificationService = $notificationService;
  }

  /**
   * Display a listing of all users with filtering.
   *
   * @param Request $request
   * @return Response
   */
  public function users(Request $request): Response
  {
    $query = User::query()->with('deactivatedBy');

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

    // Sorting
    $sortField = $request->get('sort', 'created_at');
    $sortDirection = $request->get('direction', 'desc');

    // Validate sort field to prevent SQL injection
    $allowedSortFields = ['name', 'email', 'role', 'is_active', 'created_at', 'deactivated_at'];
    if (!in_array($sortField, $allowedSortFields)) {
      $sortField = 'created_at';
    }

    // Validate sort direction
    $sortDirection = strtolower($sortDirection) === 'asc' ? 'asc' : 'desc';

    $users = $query->orderBy($sortField, $sortDirection)->paginate(15);

    return Inertia::render('Admin/Users', [
      'users' => $users,
      'filters' => [
        'role' => $request->role ?? '',
        'department' => $request->department ?? '',
        'school_year' => $request->school_year ?? '',
        'is_active' => $request->is_active ?? '',
        'search' => $request->search ?? '',
        'sort' => $sortField,
        'direction' => $sortDirection,
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

  /**
   * Toggle user activation status.
   * When deactivating, returns affected concept papers with pending stages.
   *
   * @param User $user
   * @return JsonResponse
   */
  public function toggleActivation(User $user): JsonResponse
  {
    // Authorization check using policy
    $this->authorize('toggleActivation', $user);

    $newStatus = !$user->is_active;
    $affectedPapers = [];

    // If deactivating, get affected concept papers
    if (!$newStatus) {
      $affectedPapers = $this->getAffectedConceptPapers($user);

      // Update user activation status
      $user->update([
        'is_active' => false,
        'deactivated_at' => now(),
        'deactivated_by' => Auth::id(),
      ]);
    } else {
      // Activating user
      $user->update([
        'is_active' => true,
        'deactivated_at' => null,
        'deactivated_by' => null,
      ]);
    }

    $status = $user->is_active ? 'activated' : 'deactivated';

    return response()->json([
      'success' => true,
      'message' => "User {$status} successfully.",
      'user' => $user->fresh(),
      'affected_papers' => $affectedPapers,
    ]);
  }

  /**
   * Get all workflow stages currently assigned to a user.
   * Returns concept papers with pending stages assigned to the user.
   *
   * @param User $user
   * @return JsonResponse
   */
  public function getAssignedStages(User $user): JsonResponse
  {
    // Authorization check using policy
    $currentUser = Auth::user();
    if (!$currentUser) {
      return response()->json([
        'error' => 'Unauthenticated.'
      ], 401);
    }
    $this->authorize('viewAssignedStages', $currentUser);

    $affectedPapers = $this->getAffectedConceptPapers($user);

    return response()->json([
      'success' => true,
      'affected_papers' => $affectedPapers,
    ]);
  }

  /**
   * Reassign a workflow stage to a different user.
   * Records the reassignment in the audit trail and sends notifications.
   *
   * @param Request $request
   * @param WorkflowStage $stage
   * @return JsonResponse
   */
  public function reassignStage(Request $request, WorkflowStage $stage): JsonResponse
  {
    // Authorization check using policy
    $this->authorize('reassign', $stage);

    // Validate the request
    $validated = $request->validate([
      'new_user_id' => ['required', 'exists:users,id'],
    ]);

    $newUser = User::findOrFail($validated['new_user_id']);

    // Validate that the new user is active
    if (!$newUser->is_active) {
      return response()->json([
        'error' => 'Cannot reassign to an inactive user.'
      ], 422);
    }

    // Validate that the new user has the matching role
    if ($newUser->role !== $stage->assigned_role) {
      return response()->json([
        'error' => "The selected user does not have the required role ({$stage->assigned_role})."
      ], 422);
    }

    // Store the previous user for notification
    $previousUser = $stage->assignedUser;

    // Perform the reassignment using WorkflowService
    $this->workflowService->reassignStage($stage, $newUser, Auth::user());

    // Send notification to the newly assigned user
    if ($previousUser) {
      $this->notificationService->sendStageReassignmentNotification(
        $stage->fresh(),
        $newUser,
        $previousUser
      );
    } else {
      // If there was no previous user, still notify the new user
      $this->notificationService->notifyStageAssignment($stage->fresh());
    }

    return response()->json([
      'success' => true,
      'message' => 'Stage reassigned successfully.',
      'stage' => $stage->fresh()->load(['assignedUser', 'conceptPaper']),
    ]);
  }

  /**
   * Helper method to get affected concept papers for a user.
   * Returns concept papers with pending stages assigned to the user.
   *
   * @param User $user
   * @return array
   */
  protected function getAffectedConceptPapers(User $user): array
  {
    // Get all pending or in-progress stages assigned to this user
    $stages = WorkflowStage::where('assigned_user_id', $user->id)
      ->whereIn('status', ['pending', 'in_progress'])
      ->with(['conceptPaper', 'conceptPaper.requisitioner'])
      ->get();

    // Group by concept paper
    $affectedPapers = [];
    foreach ($stages as $stage) {
      $paperId = $stage->conceptPaper->id;

      if (!isset($affectedPapers[$paperId])) {
        $affectedPapers[$paperId] = [
          'id' => $stage->conceptPaper->id,
          'title' => $stage->conceptPaper->title,
          'tracking_number' => $stage->conceptPaper->tracking_number,
          'requisitioner' => $stage->conceptPaper->requisitioner->name ?? 'Unknown',
          'stages' => [],
        ];
      }

      $affectedPapers[$paperId]['stages'][] = [
        'id' => $stage->id,
        'stage_name' => $stage->stage_name,
        'stage_order' => $stage->stage_order,
        'status' => $stage->status,
        'assigned_role' => $stage->assigned_role,
      ];
    }

    return array_values($affectedPapers);
  }
}

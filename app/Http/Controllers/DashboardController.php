<?php

namespace App\Http\Controllers;

use App\Models\ConceptPaper;
use App\Models\WorkflowStage;
use App\Models\AuditLog;
use App\Services\ConceptPaperService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
  protected ConceptPaperService $conceptPaperService;

  public function __construct(ConceptPaperService $conceptPaperService)
  {
    $this->conceptPaperService = $conceptPaperService;
  }

  /**
   * Display role-specific dashboard with pending tasks, overdue items, and recent activity.
   *
   * @param Request $request
   * @return Response
   */
  public function index(Request $request): Response
  {
    $user = Auth::user();

    // Get role-specific data
    $dashboardData = $this->getRoleSpecificData($user);

    // Get pending tasks for the user
    $pendingTasks = $this->getPendingTasks($user);

    // Get overdue items
    $overdueItems = $this->getOverdueItems($user);

    // Get recent activity
    $recentActivity = $this->getRecentActivity($user);

    // Get statistics
    $statistics = $this->getStatistics($user);

    return Inertia::render('Dashboard', [
      'user' => [
        'id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'department' => $user->department,
      ],
      'dashboard_data' => $dashboardData,
      'pending_tasks' => $pendingTasks,
      'overdue_items' => $overdueItems,
      'recent_activity' => $recentActivity,
      'statistics' => $statistics,
    ]);
  }

  /**
   * Get statistics for dashboard widgets.
   *
   * @param Request $request
   * @return array
   */
  public function statistics(Request $request): array
  {
    $user = Auth::user();
    return $this->getStatistics($user);
  }

  /**
   * Get role-specific dashboard data.
   *
   * @param \App\Models\User $user
   * @return array
   */
  protected function getRoleSpecificData($user): array
  {
    $role = $user->role;

    switch ($role) {
      case 'requisitioner':
        return $this->getRequisitionerData($user);

      case 'admin':
        return $this->getAdminData($user);

      case 'sps':
      case 'vp_acad':
      case 'auditor':
      case 'accounting':
        return $this->getApproverData($user);

      default:
        return [];
    }
  }

  /**
   * Get dashboard data for requisitioners.
   *
   * @param \App\Models\User $user
   * @return array
   */
  protected function getRequisitionerData($user): array
  {
    $myPapers = ConceptPaper::where('requisitioner_id', $user->id)
      ->with(['currentStage', 'requisitioner'])
      ->orderBy('submitted_at', 'desc')
      ->limit(10)
      ->get();

    $totalSubmitted = ConceptPaper::where('requisitioner_id', $user->id)->count();
    $inProgress = ConceptPaper::where('requisitioner_id', $user->id)
      ->where('status', 'in_progress')
      ->count();
    $completed = ConceptPaper::where('requisitioner_id', $user->id)
      ->where('status', 'completed')
      ->count();
    $returned = ConceptPaper::where('requisitioner_id', $user->id)
      ->where('status', 'returned')
      ->count();

    return [
      'role_type' => 'requisitioner',
      'my_papers' => $myPapers->map(function ($paper) {
        return [
          'id' => $paper->id,
          'tracking_number' => $paper->tracking_number,
          'title' => $paper->title,
          'status' => $paper->status,
          'submitted_at' => $paper->submitted_at,
          'current_stage' => $paper->currentStage ? [
            'stage_name' => $paper->currentStage->stage_name,
            'status' => $paper->currentStage->status,
            'deadline' => $paper->currentStage->deadline,
            'is_overdue' => $paper->currentStage->isOverdue(),
          ] : null,
          'is_overdue' => $paper->isOverdue(),
        ];
      }),
      'counts' => [
        'total' => $totalSubmitted,
        'in_progress' => $inProgress,
        'completed' => $completed,
        'returned' => $returned,
      ],
    ];
  }

  /**
   * Get dashboard data for approvers (SPS, VP Acad, Auditor, Accounting).
   *
   * @param \App\Models\User $user
   * @return array
   */
  protected function getApproverData($user): array
  {
    // Get stages assigned to this user's role that are pending or in progress
    $assignedStages = WorkflowStage::where('assigned_role', $user->role)
      ->whereIn('status', ['pending', 'in_progress'])
      ->with(['conceptPaper.requisitioner', 'assignedUser'])
      ->orderBy('deadline', 'asc')
      ->limit(10)
      ->get();

    $totalAssigned = WorkflowStage::where('assigned_role', $user->role)
      ->whereIn('status', ['pending', 'in_progress'])
      ->count();

    $completedByMe = WorkflowStage::where('assigned_role', $user->role)
      ->where('status', 'completed')
      ->count();

    $overdueCount = WorkflowStage::where('assigned_role', $user->role)
      ->whereIn('status', ['pending', 'in_progress'])
      ->where('deadline', '<', now())
      ->count();

    return [
      'role_type' => 'approver',
      'assigned_stages' => $assignedStages->map(function ($stage) {
        return [
          'id' => $stage->id,
          'stage_name' => $stage->stage_name,
          'stage_order' => $stage->stage_order,
          'status' => $stage->status,
          'deadline' => $stage->deadline,
          'is_overdue' => $stage->isOverdue(),
          'concept_paper' => [
            'id' => $stage->conceptPaper->id,
            'tracking_number' => $stage->conceptPaper->tracking_number,
            'title' => $stage->conceptPaper->title,
            'department' => $stage->conceptPaper->department,
            'nature_of_request' => $stage->conceptPaper->nature_of_request,
            'requisitioner' => [
              'name' => $stage->conceptPaper->requisitioner->name,
            ],
          ],
        ];
      }),
      'counts' => [
        'total_assigned' => $totalAssigned,
        'completed' => $completedByMe,
        'overdue' => $overdueCount,
      ],
    ];
  }

  /**
   * Get dashboard data for administrators.
   *
   * @param \App\Models\User $user
   * @return array
   */
  protected function getAdminData($user): array
  {
    // Get all concept papers with their current stages
    $allPapers = ConceptPaper::with(['currentStage', 'requisitioner'])
      ->orderBy('submitted_at', 'desc')
      ->limit(10)
      ->get();

    // Get comprehensive statistics
    $stats = $this->conceptPaperService->getStatistics();

    return [
      'role_type' => 'admin',
      'recent_papers' => $allPapers->map(function ($paper) {
        return [
          'id' => $paper->id,
          'tracking_number' => $paper->tracking_number,
          'title' => $paper->title,
          'status' => $paper->status,
          'submitted_at' => $paper->submitted_at,
          'requisitioner' => [
            'name' => $paper->requisitioner->name,
          ],
          'current_stage' => $paper->currentStage ? [
            'stage_name' => $paper->currentStage->stage_name,
            'status' => $paper->currentStage->status,
            'is_overdue' => $paper->currentStage->isOverdue(),
          ] : null,
          'is_overdue' => $paper->isOverdue(),
        ];
      }),
      'system_statistics' => $stats,
    ];
  }

  /**
   * Get pending tasks for the user based on their role.
   *
   * @param \App\Models\User $user
   * @return array
   */
  protected function getPendingTasks($user): array
  {
    if ($user->hasRole('requisitioner')) {
      // Requisitioners see their papers that need attention (returned)
      $tasks = ConceptPaper::where('requisitioner_id', $user->id)
        ->where('status', 'returned')
        ->with(['currentStage'])
        ->orderBy('submitted_at', 'desc')
        ->limit(5)
        ->get();

      return $tasks->map(function ($paper) {
        return [
          'id' => $paper->id,
          'type' => 'returned_paper',
          'tracking_number' => $paper->tracking_number,
          'title' => $paper->title,
          'status' => $paper->status,
          'submitted_at' => $paper->submitted_at,
        ];
      })->toArray();
    }

    if ($user->hasRole('admin')) {
      // Admins see overdue stages across all papers
      $tasks = WorkflowStage::whereIn('status', ['pending', 'in_progress'])
        ->where('deadline', '<', now())
        ->with(['conceptPaper.requisitioner'])
        ->orderBy('deadline', 'asc')
        ->limit(5)
        ->get();

      return $tasks->map(function ($stage) {
        return [
          'id' => $stage->id,
          'type' => 'overdue_stage',
          'stage_name' => $stage->stage_name,
          'assigned_role' => $stage->assigned_role,
          'deadline' => $stage->deadline,
          'concept_paper' => [
            'id' => $stage->conceptPaper->id,
            'tracking_number' => $stage->conceptPaper->tracking_number,
            'title' => $stage->conceptPaper->title,
          ],
        ];
      })->toArray();
    }

    // Approvers see stages assigned to their role
    $tasks = WorkflowStage::where('assigned_role', $user->role)
      ->whereIn('status', ['pending', 'in_progress'])
      ->with(['conceptPaper.requisitioner'])
      ->orderBy('deadline', 'asc')
      ->limit(5)
      ->get();

    return $tasks->map(function ($stage) {
      return [
        'id' => $stage->id,
        'type' => 'pending_approval',
        'stage_name' => $stage->stage_name,
        'status' => $stage->status,
        'deadline' => $stage->deadline,
        'is_overdue' => $stage->isOverdue(),
        'concept_paper' => [
          'id' => $stage->conceptPaper->id,
          'tracking_number' => $stage->conceptPaper->tracking_number,
          'title' => $stage->conceptPaper->title,
          'requisitioner' => [
            'name' => $stage->conceptPaper->requisitioner->name,
          ],
        ],
      ];
    })->toArray();
  }

  /**
   * Get overdue items for the user.
   *
   * @param \App\Models\User $user
   * @return array
   */
  protected function getOverdueItems($user): array
  {
    if ($user->hasRole('admin')) {
      // Admins see all overdue stages
      $overdueStages = WorkflowStage::whereIn('status', ['pending', 'in_progress'])
        ->where('deadline', '<', now())
        ->with(['conceptPaper.requisitioner'])
        ->orderBy('deadline', 'asc')
        ->limit(10)
        ->get();
    } elseif ($user->hasRole('requisitioner')) {
      // Requisitioners see their overdue papers
      $overdueStages = WorkflowStage::whereHas('conceptPaper', function ($query) use ($user) {
        $query->where('requisitioner_id', $user->id);
      })
        ->whereIn('status', ['pending', 'in_progress'])
        ->where('deadline', '<', now())
        ->with(['conceptPaper.requisitioner'])
        ->orderBy('deadline', 'asc')
        ->limit(10)
        ->get();
    } else {
      // Approvers see overdue stages for their role
      $overdueStages = WorkflowStage::where('assigned_role', $user->role)
        ->whereIn('status', ['pending', 'in_progress'])
        ->where('deadline', '<', now())
        ->with(['conceptPaper.requisitioner'])
        ->orderBy('deadline', 'asc')
        ->limit(10)
        ->get();
    }

    return $overdueStages->map(function ($stage) {
      return [
        'id' => $stage->id,
        'stage_name' => $stage->stage_name,
        'assigned_role' => $stage->assigned_role,
        'deadline' => $stage->deadline,
        'days_overdue' => now()->diffInDays($stage->deadline),
        'concept_paper' => [
          'id' => $stage->conceptPaper->id,
          'tracking_number' => $stage->conceptPaper->tracking_number,
          'title' => $stage->conceptPaper->title,
          'requisitioner' => [
            'name' => $stage->conceptPaper->requisitioner->name,
          ],
        ],
      ];
    })->toArray();
  }

  /**
   * Get recent activity for the user.
   *
   * @param \App\Models\User $user
   * @return array
   */
  protected function getRecentActivity($user): array
  {
    if ($user->hasRole('admin')) {
      // Admins see all recent activity
      $recentLogs = AuditLog::with(['conceptPaper', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    } elseif ($user->hasRole('requisitioner')) {
      // Requisitioners see activity on their papers
      $recentLogs = AuditLog::whereHas('conceptPaper', function ($query) use ($user) {
        $query->where('requisitioner_id', $user->id);
      })
        ->with(['conceptPaper', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    } else {
      // Approvers see activity on papers they're involved with
      $recentLogs = AuditLog::whereHas('conceptPaper.stages', function ($query) use ($user) {
        $query->where('assigned_role', $user->role);
      })
        ->with(['conceptPaper', 'user'])
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
    }

    return $recentLogs->map(function ($log) {
      return [
        'id' => $log->id,
        'action' => $log->action,
        'stage_name' => $log->stage_name,
        'remarks' => $log->remarks,
        'created_at' => $log->created_at,
        'user' => [
          'name' => $log->user->name,
        ],
        'concept_paper' => [
          'id' => $log->conceptPaper->id,
          'tracking_number' => $log->conceptPaper->tracking_number,
          'title' => $log->conceptPaper->title,
        ],
      ];
    })->toArray();
  }

  /**
   * Get statistics based on user role.
   *
   * @param \App\Models\User $user
   * @return array
   */
  protected function getStatistics($user): array
  {
    if ($user->hasRole('admin')) {
      // Admins get full system statistics
      return $this->conceptPaperService->getStatistics();
    }

    if ($user->hasRole('requisitioner')) {
      // Requisitioners get their own statistics
      $total = ConceptPaper::where('requisitioner_id', $user->id)->count();
      $pending = ConceptPaper::where('requisitioner_id', $user->id)
        ->where('status', 'pending')
        ->count();
      $inProgress = ConceptPaper::where('requisitioner_id', $user->id)
        ->where('status', 'in_progress')
        ->count();
      $completed = ConceptPaper::where('requisitioner_id', $user->id)
        ->where('status', 'completed')
        ->count();
      $returned = ConceptPaper::where('requisitioner_id', $user->id)
        ->where('status', 'returned')
        ->count();

      return [
        'total_papers' => $total,
        'by_status' => [
          'pending' => $pending,
          'in_progress' => $inProgress,
          'completed' => $completed,
          'returned' => $returned,
        ],
      ];
    }

    // Approvers get statistics for their role
    $totalAssigned = WorkflowStage::where('assigned_role', $user->role)->count();
    $pending = WorkflowStage::where('assigned_role', $user->role)
      ->where('status', 'pending')
      ->count();
    $inProgress = WorkflowStage::where('assigned_role', $user->role)
      ->where('status', 'in_progress')
      ->count();
    $completed = WorkflowStage::where('assigned_role', $user->role)
      ->where('status', 'completed')
      ->count();
    $overdue = WorkflowStage::where('assigned_role', $user->role)
      ->whereIn('status', ['pending', 'in_progress'])
      ->where('deadline', '<', now())
      ->count();

    return [
      'total_stages' => $totalAssigned,
      'by_status' => [
        'pending' => $pending,
        'in_progress' => $inProgress,
        'completed' => $completed,
        'overdue' => $overdue,
      ],
    ];
  }
}

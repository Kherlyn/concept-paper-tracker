<?php

namespace App\Http\Controllers;

use App\Models\ConceptPaper;
use App\Services\ConceptPaperService;
use App\Http\Requests\StoreConceptPaperRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class ConceptPaperController extends Controller
{
  use AuthorizesRequests;

  protected ConceptPaperService $conceptPaperService;

  public function __construct(ConceptPaperService $conceptPaperService)
  {
    $this->conceptPaperService = $conceptPaperService;
  }

  /**
   * Display a listing of concept papers filtered by user role.
   *
   * @param Request $request
   * @return Response
   */
  public function index(Request $request): Response
  {
    $user = Auth::user();
    $papers = $this->conceptPaperService->getUserPapers($user);

    // Apply additional filters if provided
    $status = $request->query('status');
    if ($status) {
      $papers = $papers->where('status', $status);
    }

    return Inertia::render('ConceptPapers/Index', [
      'papers' => $papers->map(function ($paper) {
        return [
          'id' => $paper->id,
          'tracking_number' => $paper->tracking_number,
          'title' => $paper->title,
          'department' => $paper->department,
          'nature_of_request' => $paper->nature_of_request,
          'status' => $paper->status,
          'submitted_at' => $paper->submitted_at,
          'requisitioner' => [
            'id' => $paper->requisitioner->id,
            'name' => $paper->requisitioner->name,
          ],
          'current_stage' => $paper->currentStage ? [
            'id' => $paper->currentStage->id,
            'stage_name' => $paper->currentStage->stage_name,
            'status' => $paper->currentStage->status,
            'deadline' => $paper->currentStage->deadline,
            'is_overdue' => $paper->currentStage->isOverdue(),
            'assigned_user_id' => $paper->currentStage->assigned_user_id,
          ] : null,
          'is_overdue' => $paper->isOverdue(),
        ];
      })->values(),
      'filters' => [
        'status' => $status,
      ],
    ]);
  }

  /**
   * Show the form for creating a new concept paper.
   *
   * @return Response
   */
  public function create(): Response
  {
    $this->authorize('create', ConceptPaper::class);

    return Inertia::render('ConceptPapers/Create');
  }

  /**
   * Store a newly created concept paper in storage.
   *
   * @param StoreConceptPaperRequest $request
   * @return RedirectResponse
   */
  public function store(StoreConceptPaperRequest $request): RedirectResponse
  {
    $validated = $request->validated();
    $user = Auth::user();
    $conceptPaper = $this->conceptPaperService->create($validated, $user);

    // Handle file upload if provided
    if ($request->hasFile('attachment')) {
      try {
        $this->conceptPaperService->attachFile(
          $conceptPaper,
          $request->file('attachment'),
          $user
        );
      } catch (\Exception $e) {
        return back()->withErrors(['attachment' => $e->getMessage()]);
      }
    }

    return redirect()->route('concept-papers.show', $conceptPaper->id)
      ->with('success', 'Concept paper submitted successfully.');
  }

  /**
   * Display the specified concept paper with audit trail.
   *
   * @param ConceptPaper $conceptPaper
   * @return Response
   */
  public function show(ConceptPaper $conceptPaper): Response
  {
    $this->authorize('view', $conceptPaper);

    $conceptPaper->load([
      'requisitioner',
      'stages.assignedUser',
      'stages.attachments.uploader',
      'attachments.uploader',
      'auditLogs.user',
      'currentStage'
    ]);

    $statusSummary = $this->conceptPaperService->getStatusSummary($conceptPaper);

    return Inertia::render('ConceptPapers/Show', [
      'paper' => [
        'id' => $conceptPaper->id,
        'tracking_number' => $conceptPaper->tracking_number,
        'title' => $conceptPaper->title,
        'department' => $conceptPaper->department,
        'nature_of_request' => $conceptPaper->nature_of_request,
        'status' => $conceptPaper->status,
        'submitted_at' => $conceptPaper->submitted_at,
        'completed_at' => $conceptPaper->completed_at,
        'requisitioner' => [
          'id' => $conceptPaper->requisitioner->id,
          'name' => $conceptPaper->requisitioner->name,
          'email' => $conceptPaper->requisitioner->email,
        ],
        'stages' => $conceptPaper->stages->map(function ($stage) {
          return [
            'id' => $stage->id,
            'stage_name' => $stage->stage_name,
            'stage_order' => $stage->stage_order,
            'assigned_role' => $stage->assigned_role,
            'status' => $stage->status,
            'started_at' => $stage->started_at,
            'completed_at' => $stage->completed_at,
            'deadline' => $stage->deadline,
            'remarks' => $stage->remarks,
            'is_overdue' => $stage->isOverdue(),
            'assigned_user' => $stage->assignedUser ? [
              'id' => $stage->assignedUser->id,
              'name' => $stage->assignedUser->name,
            ] : null,
            'attachments' => $stage->attachments->map(function ($attachment) {
              return [
                'id' => $attachment->id,
                'file_name' => $attachment->file_name,
                'file_size' => $attachment->file_size,
                'uploaded_at' => $attachment->created_at,
                'download_url' => $attachment->getUrl(),
                'uploader' => [
                  'name' => $attachment->uploader->name,
                ],
              ];
            }),
          ];
        }),
        'attachments' => $conceptPaper->attachments->map(function ($attachment) {
          return [
            'id' => $attachment->id,
            'file_name' => $attachment->file_name,
            'file_size' => $attachment->file_size,
            'uploaded_at' => $attachment->created_at,
            'download_url' => $attachment->getUrl(),
            'uploader' => [
              'name' => $attachment->uploader->name,
            ],
          ];
        }),
        'audit_logs' => $conceptPaper->auditLogs->map(function ($log) {
          return [
            'id' => $log->id,
            'action' => $log->action,
            'stage_name' => $log->stage_name,
            'remarks' => $log->remarks,
            'created_at' => $log->created_at,
            'user' => [
              'name' => $log->user->name,
            ],
          ];
        }),
      ],
      'status_summary' => $statusSummary,
    ]);
  }

  /**
   * Update the specified concept paper (limited fields).
   *
   * @param Request $request
   * @param ConceptPaper $conceptPaper
   * @return RedirectResponse
   */
  public function update(Request $request, ConceptPaper $conceptPaper): RedirectResponse
  {
    $this->authorize('update', $conceptPaper);

    $validated = $request->validate([
      'department' => 'sometimes|required|string|max:255',
      'title' => 'sometimes|required|string',
      'nature_of_request' => 'sometimes|required|in:regular,urgent,emergency',
    ]);

    $conceptPaper->update($validated);

    return back()->with('success', 'Concept paper updated successfully.');
  }

  /**
   * Soft delete the specified concept paper (admin only).
   *
   * @param ConceptPaper $conceptPaper
   * @return RedirectResponse
   */
  public function destroy(ConceptPaper $conceptPaper): RedirectResponse
  {
    $this->authorize('delete', $conceptPaper);

    $conceptPaper->delete();

    return redirect()->route('concept-papers.index')
      ->with('success', 'Concept paper deleted successfully.');
  }
}

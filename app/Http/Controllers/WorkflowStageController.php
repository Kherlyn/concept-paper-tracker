<?php

namespace App\Http\Controllers;

use App\Models\WorkflowStage;
use App\Services\WorkflowService;
use App\Services\ConceptPaperService;
use App\Http\Requests\CompleteStageRequest;
use App\Http\Requests\ReturnStageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class WorkflowStageController extends Controller
{
  use AuthorizesRequests;

  protected WorkflowService $workflowService;
  protected ConceptPaperService $conceptPaperService;

  public function __construct(
    WorkflowService $workflowService,
    ConceptPaperService $conceptPaperService
  ) {
    $this->workflowService = $workflowService;
    $this->conceptPaperService = $conceptPaperService;
  }

  /**
   * Display the specified workflow stage details.
   *
   * @param WorkflowStage $workflowStage
   * @return Response
   */
  public function show(WorkflowStage $workflowStage): Response
  {
    $workflowStage->load([
      'conceptPaper.requisitioner',
      'assignedUser',
      'attachments.uploader'
    ]);

    return Inertia::render('WorkflowStages/Show', [
      'stage' => [
        'id' => $workflowStage->id,
        'stage_name' => $workflowStage->stage_name,
        'stage_order' => $workflowStage->stage_order,
        'assigned_role' => $workflowStage->assigned_role,
        'status' => $workflowStage->status,
        'started_at' => $workflowStage->started_at,
        'completed_at' => $workflowStage->completed_at,
        'deadline' => $workflowStage->deadline,
        'remarks' => $workflowStage->remarks,
        'is_overdue' => $workflowStage->isOverdue(),
        'assigned_user' => $workflowStage->assignedUser ? [
          'id' => $workflowStage->assignedUser->id,
          'name' => $workflowStage->assignedUser->name,
          'email' => $workflowStage->assignedUser->email,
        ] : null,
        'concept_paper' => [
          'id' => $workflowStage->conceptPaper->id,
          'tracking_number' => $workflowStage->conceptPaper->tracking_number,
          'title' => $workflowStage->conceptPaper->title,
          'department' => $workflowStage->conceptPaper->department,
          'requisitioner' => [
            'name' => $workflowStage->conceptPaper->requisitioner->name,
          ],
        ],
        'attachments' => $workflowStage->attachments->map(function ($attachment) {
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
      ],
      'can' => [
        'complete' => Gate::allows('complete', $workflowStage),
        'return' => Gate::allows('return', $workflowStage),
        'addAttachment' => Gate::allows('addAttachment', $workflowStage),
      ],
    ]);
  }

  /**
   * Mark the workflow stage as complete and advance to next stage.
   *
   * @param CompleteStageRequest $request
   * @param WorkflowStage $workflowStage
   * @return RedirectResponse
   */
  public function complete(CompleteStageRequest $request, WorkflowStage $workflowStage): RedirectResponse
  {
    $validated = $request->validated();

    $this->workflowService->advanceToNextStage(
      $workflowStage,
      $validated['remarks'] ?? null,
      $validated['signature'] ?? null
    );

    return redirect()
      ->route('concept-papers.show', $workflowStage->conceptPaper->id)
      ->with('success', 'Stage approved successfully with digital signature. Workflow advanced to next stage.');
  }

  /**
   * Return the workflow stage to the previous stage with remarks.
   *
   * @param ReturnStageRequest $request
   * @param WorkflowStage $workflowStage
   * @return RedirectResponse
   */
  public function return(ReturnStageRequest $request, WorkflowStage $workflowStage): RedirectResponse
  {
    $validated = $request->validated();

    $this->workflowService->returnToPreviousStage(
      $workflowStage,
      $validated['remarks']
    );

    return redirect()
      ->route('concept-papers.show', $workflowStage->conceptPaper->id)
      ->with('success', 'Workflow returned to previous stage.');
  }

  /**
   * Reject the workflow stage and concept paper.
   *
   * @param Request $request
   * @param WorkflowStage $workflowStage
   * @return RedirectResponse
   */
  public function reject(Request $request, WorkflowStage $workflowStage): RedirectResponse
  {
    $this->authorize('complete', $workflowStage);

    $validated = $request->validate([
      'rejection_reason' => 'required|string|max:1000',
    ]);

    $this->workflowService->rejectStage(
      $workflowStage,
      $validated['rejection_reason']
    );

    return redirect()
      ->route('concept-papers.show', $workflowStage->conceptPaper->id)
      ->with('success', 'Concept paper has been rejected.');
  }

  /**
   * Upload a supporting document to the workflow stage.
   *
   * @param Request $request
   * @param WorkflowStage $workflowStage
   * @return RedirectResponse
   */
  public function addAttachment(Request $request, WorkflowStage $workflowStage): RedirectResponse
  {
    $this->authorize('addAttachment', $workflowStage);

    $validated = $request->validate([
      'attachment' => 'required|file|mimes:pdf|max:10240', // 10MB max
    ]);

    try {
      $file = $request->file('attachment');
      $user = Auth::user();

      // Validate file type (PDF only)
      if ($file->getMimeType() !== 'application/pdf') {
        return back()->withErrors(['attachment' => 'Only PDF files are allowed.']);
      }

      // Validate file size (10MB max)
      $maxSize = 10 * 1024 * 1024; // 10MB in bytes
      if ($file->getSize() > $maxSize) {
        return back()->withErrors(['attachment' => 'File size must not exceed 10MB.']);
      }

      // Generate unique file name
      $fileName = $file->getClientOriginalName();
      $uniqueName = \Illuminate\Support\Str::uuid() . '_' . $fileName;

      // Store file in workflow_stages directory
      $filePath = $file->storeAs(
        'workflow_stages/' . $workflowStage->id,
        $uniqueName,
        'local'
      );

      // Create attachment record
      \App\Models\Attachment::create([
        'attachable_type' => WorkflowStage::class,
        'attachable_id' => $workflowStage->id,
        'file_name' => $fileName,
        'file_path' => $filePath,
        'file_size' => $file->getSize(),
        'mime_type' => $file->getMimeType(),
        'uploaded_by' => $user->id,
      ]);

      return back()->with('success', 'Attachment uploaded successfully.');
    } catch (\Exception $e) {
      return back()->withErrors(['attachment' => 'Failed to upload attachment: ' . $e->getMessage()]);
    }
  }
}

<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Redirect authenticated users to dashboard
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }

    // Show landing page to unauthenticated users
    return Inertia::render('Landing', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
    ]);
})->name('landing');

// ============================================================================
// Authenticated Routes
// ============================================================================
Route::middleware(['auth', 'verified'])->group(function () {

    // ------------------------------------------------------------------------
    // Dashboard Routes
    // ------------------------------------------------------------------------
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/dashboard/statistics', [\App\Http\Controllers\DashboardController::class, 'statistics'])
        ->name('dashboard.statistics');

    // ------------------------------------------------------------------------
    // Profile Routes
    // ------------------------------------------------------------------------
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ------------------------------------------------------------------------
    // Concept Paper Routes
    // ------------------------------------------------------------------------
    // index: List papers (filtered by user role)
    // create: Show submission form (requisitioners only)
    // store: Submit new concept paper (requisitioners only)
    // show: View paper details (authorized users only)
    // update: Update paper (limited fields, requisitioner before first approval)
    // destroy: Soft delete (admin only)
    Route::resource('concept-papers', \App\Http\Controllers\ConceptPaperController::class)
        ->only(['index', 'create', 'show', 'update', 'destroy']);

    // Apply rate limiting to concept paper submission
    Route::post('concept-papers', [\App\Http\Controllers\ConceptPaperController::class, 'store'])
        ->middleware('throttle:10,1')
        ->name('concept-papers.store');

    // ------------------------------------------------------------------------
    // Workflow Stage Routes
    // ------------------------------------------------------------------------
    // show: Display stage details
    // complete: Mark stage complete and advance workflow
    // return: Send back to previous stage with remarks
    // addAttachment: Upload supporting documents
    Route::prefix('workflow-stages')->name('workflow-stages.')->group(function () {
        Route::get('/{workflowStage}', [\App\Http\Controllers\WorkflowStageController::class, 'show'])
            ->name('show');
        Route::post('/{workflowStage}/complete', [\App\Http\Controllers\WorkflowStageController::class, 'complete'])
            ->middleware('throttle:20,1')
            ->name('complete');
        Route::post('/{workflowStage}/return', [\App\Http\Controllers\WorkflowStageController::class, 'return'])
            ->middleware('throttle:20,1')
            ->name('return');
        Route::post('/{workflowStage}/attachments', [\App\Http\Controllers\WorkflowStageController::class, 'addAttachment'])
            ->middleware('throttle:10,1')
            ->name('add-attachment');
    });

    // ------------------------------------------------------------------------
    // Notification Routes
    // ------------------------------------------------------------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [\App\Http\Controllers\NotificationController::class, 'index'])
            ->name('index');
        Route::post('/{id}/mark-as-read', [\App\Http\Controllers\NotificationController::class, 'markAsRead'])
            ->name('mark-as-read');
        Route::post('/mark-all-as-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])
            ->name('mark-all-as-read');
        Route::delete('/{id}', [\App\Http\Controllers\NotificationController::class, 'destroy'])
            ->name('destroy');
        Route::delete('/delete-all-read', [\App\Http\Controllers\NotificationController::class, 'deleteAllRead'])
            ->name('delete-all-read');
    });

    // ------------------------------------------------------------------------
    // Attachment Routes
    // ------------------------------------------------------------------------
    Route::get('/attachments/{attachment}/download', [\App\Http\Controllers\AttachmentController::class, 'download'])
        ->name('attachments.download');

    // ------------------------------------------------------------------------
    // User Guide Routes
    // ------------------------------------------------------------------------
    Route::get('/user-guide', [\App\Http\Controllers\UserGuideController::class, 'index'])
        ->name('user-guide');
    Route::get('/user-guide/{section}', [\App\Http\Controllers\UserGuideController::class, 'show'])
        ->name('user-guide.section');

    // ------------------------------------------------------------------------
    // Admin Routes (Role-Based Access)
    // ------------------------------------------------------------------------
    Route::prefix('admin')->name('admin.')->middleware('role:admin')->group(function () {

        // User Management
        Route::get('/users', [\App\Http\Controllers\AdminController::class, 'users'])
            ->name('users');
        Route::post('/users', [\App\Http\Controllers\AdminController::class, 'store'])
            ->middleware('throttle:10,1')
            ->name('users.store');
        Route::put('/users/{user}', [\App\Http\Controllers\AdminController::class, 'update'])
            ->middleware('throttle:20,1')
            ->name('users.update');
        Route::post('/users/{user}/toggle-active', [\App\Http\Controllers\AdminController::class, 'toggleActive'])
            ->middleware('throttle:20,1')
            ->name('users.toggle-active');

        // Reports
        Route::get('/reports', [\App\Http\Controllers\AdminController::class, 'reports'])
            ->name('reports');
        Route::get('/reports/csv', [\App\Http\Controllers\AdminController::class, 'downloadCsv'])
            ->middleware('throttle:5,1')
            ->name('reports.csv');
        Route::get('/reports/pdf/{conceptPaper}', [\App\Http\Controllers\AdminController::class, 'downloadPdf'])
            ->middleware('throttle:10,1')
            ->name('reports.pdf');
    });
});

require __DIR__ . '/auth.php';

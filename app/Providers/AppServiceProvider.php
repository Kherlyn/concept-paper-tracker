<?php

namespace App\Providers;

use App\Models\Attachment;
use App\Models\ConceptPaper;
use App\Models\WorkflowStage;
use App\Observers\AttachmentObserver;
use App\Observers\ConceptPaperObserver;
use App\Observers\WorkflowStageObserver;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind DocumentPreviewService interface to implementation
        $this->app->bind(
            \App\Services\Contracts\DocumentPreviewServiceInterface::class,
            \App\Services\DocumentPreviewService::class
        );

        // Bind AnnotationService interface to implementation
        $this->app->bind(
            \App\Services\Contracts\AnnotationServiceInterface::class,
            \App\Services\AnnotationService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        Vite::prefetch(concurrency: 3);

        // Register model observers for audit trail logging
        ConceptPaper::observe(ConceptPaperObserver::class);
        WorkflowStage::observe(WorkflowStageObserver::class);
        Attachment::observe(AttachmentObserver::class);
    }
}

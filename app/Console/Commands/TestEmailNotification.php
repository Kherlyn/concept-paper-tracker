<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ConceptPaper;
use App\Models\WorkflowStage;
use App\Notifications\StageAssignedNotification;
use App\Notifications\StageOverdueNotification;
use App\Notifications\PaperCompletedNotification;
use App\Notifications\PaperReturnedNotification;
use Illuminate\Console\Command;

class TestEmailNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notification:test {type=all : The notification type to test (all, assigned, overdue, completed, returned)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email notifications by sending sample notifications';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $type = $this->argument('type');

        // Get a test user (first user in database)
        $user = User::first();

        if (!$user) {
            $this->error('No users found in database. Please run seeders first.');
            return self::FAILURE;
        }

        // Get a test concept paper
        $conceptPaper = ConceptPaper::with('stages')->first();

        if (!$conceptPaper) {
            $this->error('No concept papers found in database. Please run seeders first.');
            return self::FAILURE;
        }

        $this->info("Testing email notifications for user: {$user->email}");
        $this->info("Using concept paper: {$conceptPaper->tracking_number}");
        $this->newLine();

        if ($type === 'all' || $type === 'assigned') {
            $this->testStageAssigned($user, $conceptPaper);
        }

        if ($type === 'all' || $type === 'overdue') {
            $this->testStageOverdue($user, $conceptPaper);
        }

        if ($type === 'all' || $type === 'completed') {
            $this->testPaperCompleted($user, $conceptPaper);
        }

        if ($type === 'all' || $type === 'returned') {
            $this->testPaperReturned($user, $conceptPaper);
        }

        $this->newLine();
        $this->info('✓ Notifications sent successfully!');
        $this->info('Check storage/logs/laravel.log to see the email content (MAIL_MAILER=log)');
        $this->info('Or check your email inbox if using SMTP configuration');

        return self::SUCCESS;
    }

    private function testStageAssigned(User $user, ConceptPaper $conceptPaper): void
    {
        $stage = $conceptPaper->stages()->first();

        if (!$stage) {
            $this->warn('⚠ Skipping StageAssignedNotification - no stages found');
            return;
        }

        $this->info('→ Sending StageAssignedNotification...');
        $user->notify(new StageAssignedNotification($stage));
    }

    private function testStageOverdue(User $user, ConceptPaper $conceptPaper): void
    {
        $stage = $conceptPaper->stages()->first();

        if (!$stage) {
            $this->warn('⚠ Skipping StageOverdueNotification - no stages found');
            return;
        }

        $this->info('→ Sending StageOverdueNotification...');
        $user->notify(new StageOverdueNotification($stage));
    }

    private function testPaperCompleted(User $user, ConceptPaper $conceptPaper): void
    {
        $this->info('→ Sending PaperCompletedNotification...');
        $user->notify(new PaperCompletedNotification($conceptPaper));
    }

    private function testPaperReturned(User $user, ConceptPaper $conceptPaper): void
    {
        $stage = $conceptPaper->stages()->first();

        if (!$stage) {
            $this->warn('⚠ Skipping PaperReturnedNotification - no stages found');
            return;
        }

        $this->info('→ Sending PaperReturnedNotification...');
        $user->notify(new PaperReturnedNotification($stage, 'This is a test remark for the returned paper.'));
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('workflow_stages', function (Blueprint $table) {
            // Add signature field for digital signatures
            $table->text('signature')->nullable()->after('remarks');

            // Add rejection fields
            $table->boolean('is_rejected')->default(false)->after('signature');
            $table->text('rejection_reason')->nullable()->after('is_rejected');
            $table->timestamp('rejected_at')->nullable()->after('rejection_reason');

            // Update status enum to include 'rejected'
            $table->enum('status', ['pending', 'in_progress', 'completed', 'returned', 'rejected'])
                ->default('pending')
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('workflow_stages', function (Blueprint $table) {
            $table->dropColumn(['signature', 'is_rejected', 'rejection_reason', 'rejected_at']);

            // Revert status enum
            $table->enum('status', ['pending', 'in_progress', 'completed', 'returned'])
                ->default('pending')
                ->change();
        });
    }
};

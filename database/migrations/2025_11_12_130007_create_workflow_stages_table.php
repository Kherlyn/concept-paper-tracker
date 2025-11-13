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
        Schema::create('workflow_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('concept_paper_id')->constrained('concept_papers')->onDelete('cascade');
            $table->string('stage_name', 100);
            $table->tinyInteger('stage_order');
            $table->string('assigned_role', 50);
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->enum('status', ['pending', 'in_progress', 'completed', 'returned'])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('deadline');
            $table->text('remarks')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('concept_paper_id');
            $table->index('assigned_role');
            $table->index('assigned_user_id');
            $table->index('status');
            $table->index('deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_stages');
    }
};

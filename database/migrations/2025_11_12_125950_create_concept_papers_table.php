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
        Schema::create('concept_papers', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_number', 50)->unique();
            $table->foreignId('requisitioner_id')->constrained('users')->onDelete('cascade');
            $table->string('department');
            $table->text('title');
            $table->enum('nature_of_request', ['regular', 'urgent', 'emergency']);
            $table->timestamp('submitted_at');
            $table->unsignedBigInteger('current_stage_id')->nullable();
            $table->enum('status', ['pending', 'in_progress', 'completed', 'returned'])->default('pending');
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('requisitioner_id');
            $table->index('status');
            $table->index('submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('concept_papers');
    }
};

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
    Schema::create('annotations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('concept_paper_id')->constrained('concept_papers')->onDelete('cascade');
      $table->foreignId('attachment_id')->constrained('attachments')->onDelete('cascade');
      $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
      $table->integer('page_number');
      $table->string('annotation_type', 50); // 'marker', 'highlight', 'discrepancy'
      $table->json('coordinates'); // {x, y, width, height, points}
      $table->text('comment')->nullable();
      $table->boolean('is_discrepancy')->default(false);
      $table->timestamps();

      // Indexes for performance
      $table->index('concept_paper_id');
      $table->index('attachment_id');
      $table->index(['attachment_id', 'page_number']);
      $table->index('is_discrepancy');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('annotations');
  }
};

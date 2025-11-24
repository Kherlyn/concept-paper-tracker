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
    Schema::table('attachments', function (Blueprint $table) {
      // Modify mime_type column to support Word document MIME types
      // The column already exists with VARCHAR(100) which is sufficient
      // This migration documents the support for additional MIME types:
      // - application/pdf (existing)
      // - application/msword (.doc)
      // - application/vnd.openxmlformats-officedocument.wordprocessingml.document (.docx)

      // No schema changes needed, but we'll add a comment for documentation
      // The validation will be handled at the application level
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    // No changes to revert as we didn't modify the schema
    // The mime_type column remains unchanged
  }
};

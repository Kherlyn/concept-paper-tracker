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
    Schema::table('concept_papers', function (Blueprint $table) {
      $table->boolean('students_involved')->default(true)->after('nature_of_request');
      $table->string('deadline_option', 50)->nullable()->after('students_involved');
      $table->timestamp('deadline_date')->nullable()->after('deadline_option');

      // Add index for performance
      $table->index('students_involved');
      $table->index('deadline_date');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('concept_papers', function (Blueprint $table) {
      $table->dropIndex(['students_involved']);
      $table->dropIndex(['deadline_date']);
      $table->dropColumn(['students_involved', 'deadline_option', 'deadline_date']);
    });
  }
};

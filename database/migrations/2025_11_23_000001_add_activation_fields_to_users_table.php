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
    Schema::table('users', function (Blueprint $table) {
      // is_active already exists from previous migration
      // Only add the new deactivation tracking fields
      if (!Schema::hasColumn('users', 'deactivated_at')) {
        $table->timestamp('deactivated_at')->nullable()->after('is_active');
      }
      if (!Schema::hasColumn('users', 'deactivated_by')) {
        $table->foreignId('deactivated_by')->nullable()->constrained('users')->onDelete('set null')->after('deactivated_at');
      }
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::table('users', function (Blueprint $table) {
      if (Schema::hasColumn('users', 'deactivated_by')) {
        $table->dropForeign(['deactivated_by']);
        $table->dropColumn('deactivated_by');
      }
      if (Schema::hasColumn('users', 'deactivated_at')) {
        $table->dropColumn('deactivated_at');
      }
      // Don't drop is_active as it was created by a previous migration
    });
  }
};

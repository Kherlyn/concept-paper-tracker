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
    Schema::table('concept_papers', function (Blueprint $table) {
      // Revert status enum
      $table->enum('status', ['pending', 'in_progress', 'completed', 'returned'])
        ->default('pending')
        ->change();
    });
  }
};

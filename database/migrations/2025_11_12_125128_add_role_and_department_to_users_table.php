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
            $table->enum('role', ['requisitioner', 'sps', 'vp_acad', 'auditor', 'accounting', 'admin'])
                ->default('requisitioner')
                ->after('password');
            $table->string('department')->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('department');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'department', 'is_active']);
        });
    }
};

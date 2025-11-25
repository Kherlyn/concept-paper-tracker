<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create default admin user if it doesn't exist
        $adminEmail = 'admin@gmail.com';

        $existingAdmin = DB::table('users')->where('email', $adminEmail)->first();

        if (!$existingAdmin) {
            DB::table('users')->insert([
                'name' => 'Admin',
                'email' => $adminEmail,
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'department' => 'IT',
                'school_year' => null,
                'student_number' => null,
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally remove the default admin user
        DB::table('users')->where('email', 'admin@gmail.com')->delete();
    }
};

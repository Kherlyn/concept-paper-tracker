<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'John Requisitioner',
                'email' => 'requisitioner@example.com',
                'password' => bcrypt('password'),
                'role' => 'requisitioner',
                'department' => 'Computer Science',
                'school_year' => '2024-2025',
                'student_number' => '2024-00001',
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Principal',
                'email' => 'sps@example.com',
                'password' => bcrypt('password'),
                'role' => 'sps',
                'department' => 'Administration',
                'school_year' => null,
                'student_number' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Michael VP',
                'email' => 'vp_acad@example.com',
                'password' => bcrypt('password'),
                'role' => 'vp_acad',
                'department' => 'Academic Affairs',
                'school_year' => '2023-2024',
                'student_number' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Emily Auditor',
                'email' => 'auditor@example.com',
                'password' => bcrypt('password'),
                'role' => 'auditor',
                'department' => 'Audit Office',
                'school_year' => null,
                'student_number' => null,
                'is_active' => true,
            ],
            [
                'name' => 'David Accountant',
                'email' => 'accounting@example.com',
                'password' => bcrypt('password'),
                'role' => 'accounting',
                'department' => 'Finance',
                'school_year' => '2024-2025',
                'student_number' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'department' => 'IT',
                'school_year' => null,
                'student_number' => null,
                'is_active' => true,
            ],
            [
                'name' => 'Jane Student',
                'email' => 'student1@example.com',
                'password' => bcrypt('password'),
                'role' => 'requisitioner',
                'department' => 'Engineering',
                'school_year' => '1st Year',
                'student_number' => '2024-00002',
                'is_active' => true,
            ],
            [
                'name' => 'Mark Graduate',
                'email' => 'student2@example.com',
                'password' => bcrypt('password'),
                'role' => 'requisitioner',
                'department' => 'Business Administration',
                'school_year' => '4th Year',
                'student_number' => '2021-00123',
                'is_active' => true,
            ],
            [
                'name' => 'Lisa Faculty',
                'email' => 'faculty@example.com',
                'password' => bcrypt('password'),
                'role' => 'requisitioner',
                'department' => 'Mathematics',
                'school_year' => '2024-2025',
                'student_number' => null,
                'is_active' => true,
            ],
        ];

        foreach ($users as $userData) {
            \App\Models\User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}

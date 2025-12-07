<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add hours column to deadline_options table
        Schema::table('deadline_options', function (Blueprint $table) {
            $table->integer('hours')->nullable()->after('label');
            // Change days to decimal to support fractional days
        });

        // Modify days column to support decimal values
        Schema::table('deadline_options', function (Blueprint $table) {
            $table->decimal('days', 8, 3)->change();
        });

        // Clear existing options and insert new ones
        DB::table('deadline_options')->truncate();

        $newOptions = [
            ['key' => '3_hours', 'label' => '3 Hours', 'hours' => 3, 'days' => 0.125, 'sort_order' => 1],
            ['key' => '6_hours', 'label' => '6 Hours', 'hours' => 6, 'days' => 0.25, 'sort_order' => 2],
            ['key' => '12_hours', 'label' => '12 Hours', 'hours' => 12, 'days' => 0.5, 'sort_order' => 3],
            ['key' => '1_day', 'label' => '1 Day', 'hours' => 24, 'days' => 1, 'sort_order' => 4],
            ['key' => '3_days', 'label' => '3 Days', 'hours' => 72, 'days' => 3, 'sort_order' => 5],
            ['key' => '1_week', 'label' => '1 Week', 'hours' => 168, 'days' => 7, 'sort_order' => 6],
            ['key' => '2_weeks', 'label' => '2 Weeks', 'hours' => 336, 'days' => 14, 'sort_order' => 7],
        ];

        foreach ($newOptions as $option) {
            DB::table('deadline_options')->insert(array_merge($option, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear and restore old options
        DB::table('deadline_options')->truncate();

        $oldOptions = [
            ['key' => '1_week', 'label' => '1 Week', 'days' => 7, 'sort_order' => 1],
            ['key' => '2_weeks', 'label' => '2 Weeks', 'days' => 14, 'sort_order' => 2],
            ['key' => '1_month', 'label' => '1 Month', 'days' => 30, 'sort_order' => 3],
            ['key' => '2_months', 'label' => '2 Months', 'days' => 60, 'sort_order' => 4],
            ['key' => '3_months', 'label' => '3 Months', 'days' => 90, 'sort_order' => 5],
        ];

        foreach ($oldOptions as $option) {
            DB::table('deadline_options')->insert(array_merge($option, [
                'hours' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }

        // Remove hours column
        Schema::table('deadline_options', function (Blueprint $table) {
            $table->dropColumn('hours');
        });

        // Revert days column back to integer
        Schema::table('deadline_options', function (Blueprint $table) {
            $table->integer('days')->change();
        });
    }
};

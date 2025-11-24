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
        Schema::create('deadline_options', function (Blueprint $table) {
            $table->id();
            $table->string('key', 50)->unique();
            $table->string('label', 100);
            $table->integer('days');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed with default deadline options from config
        $defaultOptions = [
            ['key' => '1_week', 'label' => '1 Week', 'days' => 7, 'sort_order' => 1],
            ['key' => '2_weeks', 'label' => '2 Weeks', 'days' => 14, 'sort_order' => 2],
            ['key' => '1_month', 'label' => '1 Month', 'days' => 30, 'sort_order' => 3],
            ['key' => '2_months', 'label' => '2 Months', 'days' => 60, 'sort_order' => 4],
            ['key' => '3_months', 'label' => '3 Months', 'days' => 90, 'sort_order' => 5],
        ];

        foreach ($defaultOptions as $option) {
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
        Schema::dropIfExists('deadline_options');
    }
};

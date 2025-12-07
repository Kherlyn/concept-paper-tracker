<?php

namespace Database\Seeders;

use App\Models\DeadlineOption;
use Illuminate\Database\Seeder;

class DeadlineOptionSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $deadlineOptions = [
      [
        'key' => '3_hours',
        'label' => '3 Hours',
        'hours' => 3,
        'days' => 0.125, // For backwards compatibility (3/24)
        'sort_order' => 1,
      ],
      [
        'key' => '6_hours',
        'label' => '6 Hours',
        'hours' => 6,
        'days' => 0.25, // For backwards compatibility (6/24)
        'sort_order' => 2,
      ],
      [
        'key' => '12_hours',
        'label' => '12 Hours',
        'hours' => 12,
        'days' => 0.5, // For backwards compatibility (12/24)
        'sort_order' => 3,
      ],
      [
        'key' => '1_day',
        'label' => '1 Day',
        'hours' => 24,
        'days' => 1,
        'sort_order' => 4,
      ],
      [
        'key' => '3_days',
        'label' => '3 Days',
        'hours' => 72,
        'days' => 3,
        'sort_order' => 5,
      ],
      [
        'key' => '1_week',
        'label' => '1 Week',
        'hours' => 168,
        'days' => 7,
        'sort_order' => 6,
      ],
      [
        'key' => '2_weeks',
        'label' => '2 Weeks',
        'hours' => 336,
        'days' => 14,
        'sort_order' => 7,
      ],
    ];

    foreach ($deadlineOptions as $option) {
      DeadlineOption::updateOrCreate(
        ['key' => $option['key']],
        $option
      );
    }
  }
}

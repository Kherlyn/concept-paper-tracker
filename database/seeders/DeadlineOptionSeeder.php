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
        'key' => '1_week',
        'label' => '1 Week',
        'days' => 7,
        'sort_order' => 1,
      ],
      [
        'key' => '2_weeks',
        'label' => '2 Weeks',
        'days' => 14,
        'sort_order' => 2,
      ],
      [
        'key' => '1_month',
        'label' => '1 Month',
        'days' => 30,
        'sort_order' => 3,
      ],
      [
        'key' => '2_months',
        'label' => '2 Months',
        'days' => 60,
        'sort_order' => 4,
      ],
      [
        'key' => '3_months',
        'label' => '3 Months',
        'days' => 90,
        'sort_order' => 5,
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

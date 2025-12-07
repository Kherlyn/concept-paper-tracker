<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Workflow Stages Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the 10-step sequential approval process for
    | concept papers. Each stage includes the stage name, assigned role,
    | maximum processing time in days, and optional skippable flag.
    |
    */

  'stages' => [
    1 => [
      'name' => 'SPS Review',
      'role' => 'sps',
      'max_days' => 1,
      'skippable' => true,
    ],
    2 => [
      'name' => 'VP Acad Review',
      'role' => 'vp_acad',
      'max_days' => 3,
    ],
    3 => [
      'name' => 'Auditing Review',
      'role' => 'auditor',
      'max_days' => 3,
    ],
    4 => [
      'name' => 'Senior VP Approval',
      'role' => 'senior_vp',
      'max_days' => 2,
    ],
    5 => [
      'name' => 'Acad Copy Distribution',
      'role' => 'vp_acad',
      'max_days' => 1,
    ],
    6 => [
      'name' => 'Auditing Copy Distribution',
      'role' => 'auditor',
      'max_days' => 1,
    ],
    7 => [
      'name' => 'Voucher Preparation',
      'role' => 'accounting',
      'max_days' => 1,
    ],
    8 => [
      'name' => 'Audit & Countersign',
      'role' => 'auditor',
      'max_days' => 1,
    ],
    9 => [
      'name' => 'Cheque Preparation',
      'role' => 'accounting',
      'max_days' => 4,
    ],
    10 => [
      'name' => 'Budget Release',
      'role' => 'accounting',
      'max_days' => 1,
    ],
  ],

  /*
    |--------------------------------------------------------------------------
    | Deadline Options Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the predefined deadline options available
    | for requisitioners when submitting concept papers. Each option includes
    | a label for display and the number of days to add to the submission date.
    |
    */

  'deadline_options' => [
    '3_hours' => [
      'label' => '3 Hours',
      'hours' => 3,
      'days' => 0.125,
    ],
    '6_hours' => [
      'label' => '6 Hours',
      'hours' => 6,
      'days' => 0.25,
    ],
    '12_hours' => [
      'label' => '12 Hours',
      'hours' => 12,
      'days' => 0.5,
    ],
    '1_day' => [
      'label' => '1 Day',
      'hours' => 24,
      'days' => 1,
    ],
    '3_days' => [
      'label' => '3 Days',
      'hours' => 72,
      'days' => 3,
    ],
    '1_week' => [
      'label' => '1 Week',
      'hours' => 168,
      'days' => 7,
    ],
    '2_weeks' => [
      'label' => '2 Weeks',
      'hours' => 336,
      'days' => 14,
    ],
  ],
];

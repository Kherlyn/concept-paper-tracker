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
    '1_week' => [
      'label' => '1 Week',
      'days' => 7,
    ],
    '2_weeks' => [
      'label' => '2 Weeks',
      'days' => 14,
    ],
    '1_month' => [
      'label' => '1 Month',
      'days' => 30,
    ],
    '2_months' => [
      'label' => '2 Months',
      'days' => 60,
    ],
    '3_months' => [
      'label' => '3 Months',
      'days' => 90,
    ],
  ],
];

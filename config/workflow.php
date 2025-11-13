<?php

return [
  /*
    |--------------------------------------------------------------------------
    | Workflow Stages Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration defines the 9-step sequential approval process for
    | concept papers. Each stage includes the stage name, assigned role,
    | and maximum processing time in days.
    |
    */

  'stages' => [
    1 => [
      'name' => 'SPS Review',
      'role' => 'sps',
      'max_days' => 1,
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
      'name' => 'Acad Copy Distribution',
      'role' => 'vp_acad',
      'max_days' => 1,
    ],
    5 => [
      'name' => 'Auditing Copy Distribution',
      'role' => 'auditor',
      'max_days' => 1,
    ],
    6 => [
      'name' => 'Voucher Preparation',
      'role' => 'accounting',
      'max_days' => 1,
    ],
    7 => [
      'name' => 'Audit & Countersign',
      'role' => 'auditor',
      'max_days' => 1,
    ],
    8 => [
      'name' => 'Cheque Preparation',
      'role' => 'accounting',
      'max_days' => 4,
    ],
    9 => [
      'name' => 'Budget Release',
      'role' => 'accounting',
      'max_days' => 1,
    ],
  ],
];

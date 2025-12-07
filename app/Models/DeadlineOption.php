<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeadlineOption extends Model
{
  use HasFactory;

  protected $fillable = [
    'key',
    'label',
    'hours',
    'days',
    'sort_order',
  ];

  protected $casts = [
    'hours' => 'integer',
    'days' => 'decimal:3',
    'sort_order' => 'integer',
  ];
}

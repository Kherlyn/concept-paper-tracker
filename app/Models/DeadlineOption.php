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
    'days',
    'sort_order',
  ];

  protected $casts = [
    'days' => 'integer',
    'sort_order' => 'integer',
  ];
}

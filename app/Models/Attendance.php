<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
  use HasFactory;
  protected $dates = ['check_in_time'];

  protected $fillable = [
    'user_id',
    'check_in_time  ',
  ];

  /**
   * Relationship with User model.
   */
  public function user()
  {
    return $this->belongsTo(User::class);
  }
}
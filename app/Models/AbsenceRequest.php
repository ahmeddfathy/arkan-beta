<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AbsenceRequest extends Model
{
  use AuthorizesRequests;

  protected $fillable = [
    'user_id',
    'absence_date',
    'status',
    'reason',
    'manager_approval',
    'leader_approval',
    'manager_rejection_reason',
    'leader_rejection_reason'
  ];

  protected $casts = [
    'absence_date' => 'date'
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  // دالة لتحديث الحالة النهائية بناءً على موافقة المدير والليدر
  public function updateFinalStatus()
  {
    if ($this->manager_approval === 'rejected' || $this->leader_approval === 'rejected') {
      $this->status = 'rejected';
    } elseif ($this->manager_approval === 'approved' && $this->leader_approval === 'approved') {
      $this->status = 'approved';
    } else {
      $this->status = 'pending';
    }
    $this->save();
  }
}

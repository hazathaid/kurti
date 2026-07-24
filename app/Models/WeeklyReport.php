<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeeklyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'murid_id',
        'fasil_id',
        'classroom_id',
        'week_start',
        'summary',
        'achievements',
        'notes',
        'read_at',
        'read_by',
        'parent_feedback',
        'feedback_at',
        'feedback_by',
    ];

    protected function casts(): array
    {
        return [
            'week_start' => 'date',
            'read_at' => 'datetime',
            'feedback_at' => 'datetime',
        ];
    }

    public function murid()
    {
        return $this->belongsTo(User::class, 'murid_id');
    }

    public function fasil()
    {
        return $this->belongsTo(User::class, 'fasil_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    public function reader()
    {
        return $this->belongsTo(User::class, 'read_by');
    }

    public function feedbackAuthor()
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }
}

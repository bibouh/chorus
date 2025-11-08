<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'member_id',
        'status',
        'arrival_time',
        'scanned_at',
        'recorded_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'arrival_time' => 'datetime',
            'scanned_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($attendance) {
            if (Auth::check() && !$attendance->recorded_by) {
                $attendance->recorded_by = Auth::id();
            }
        });
    }

    /**
     * Get the event for this attendance.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the member for this attendance.
     */
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the user who recorded this attendance.
     */
    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}

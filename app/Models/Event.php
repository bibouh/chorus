<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event_type_id',
        'date',
        'time',
        'is_recurring',
        'parent_event_id',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'time' => 'datetime',
            'is_recurring' => 'boolean',
        ];
    }

    /**
     * Get the event type for this event.
     */
    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    /**
     * Get the user who created this event.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parent event (for recurring events).
     */
    public function parentEvent()
    {
        return $this->belongsTo(Event::class, 'parent_event_id');
    }

    /**
     * Get the child events (for recurring events).
     */
    public function childEvents()
    {
        return $this->hasMany(Event::class, 'parent_event_id');
    }

    /**
     * Get the attendances for this event.
     */
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the recurring event schedules for this event.
     */
    public function recurringEventSchedules()
    {
        return $this->hasMany(RecurringEventSchedule::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringEventSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'day_of_week',
        'end_date',
    ];

    protected function casts(): array
    {
        return [
            'end_date' => 'date',
        ];
    }

    /**
     * Get the event for this schedule.
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}

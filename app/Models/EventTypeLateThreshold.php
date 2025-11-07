<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventTypeLateThreshold extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type_id',
        'threshold_minutes',
    ];

    /**
     * Get the event type for this threshold.
     */
    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }
}

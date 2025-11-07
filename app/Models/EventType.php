<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'icon',
    ];

    /**
     * Get the events for this event type.
     */
    public function events()
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Get the late threshold for this event type.
     */
    public function lateThreshold()
    {
        return $this->hasOne(EventTypeLateThreshold::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LateDetectionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_enabled',
        'default_threshold_minutes',
        'auto_mark_late',
        'send_notifications',
        'use_different_thresholds_by_type',
    ];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
            'auto_mark_late' => 'boolean',
            'send_notifications' => 'boolean',
            'use_different_thresholds_by_type' => 'boolean',
        ];
    }

    /**
     * Get the singleton instance.
     */
    public static function getInstance()
    {
        return static::firstOrCreate([], [
            'is_enabled' => true,
            'default_threshold_minutes' => 15,
            'auto_mark_late' => true,
            'send_notifications' => true,
            'use_different_thresholds_by_type' => false,
        ]);
    }
}

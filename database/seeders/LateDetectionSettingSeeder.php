<?php

namespace Database\Seeders;

use App\Models\LateDetectionSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LateDetectionSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        LateDetectionSetting::firstOrCreate(
            [],
            [
                'is_enabled' => true,
                'default_threshold_minutes' => 15,
                'auto_mark_late' => true,
                'send_notifications' => true,
                'use_different_thresholds_by_type' => false,
            ]
        );
    }
}

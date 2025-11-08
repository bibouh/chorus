<?php

namespace App\JsonApi\V1\LateDetectionSettings;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class LateDetectionSettingRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $setting = $this->model();
        $uniqueThresholdMinutes = Rule::unique('late_detection_settings', 'id');

        if ($setting) {
            $uniqueThresholdMinutes->ignoreModel($setting);
        }
        return [
            'is_enabled' => ['boolean'],
            'default_threshold_minutes' => [
                $setting ? 'nullable' : 'required',
                'integer',
                'min:1',
                'max:1440',
            ],
            'auto_mark_late' => ['boolean'],
            'send_notifications' => ['boolean'],
            'use_different_thresholds_by_type' => ['boolean'],
        ];
    }

}

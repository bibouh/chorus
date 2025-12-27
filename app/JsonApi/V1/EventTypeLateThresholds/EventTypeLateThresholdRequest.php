<?php

namespace App\JsonApi\V1\EventTypeLateThresholds;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;

class EventTypeLateThresholdRequest extends ResourceRequest
{
    /**
     * Get the validation rules for the resource.
     */
    public function rules(): array
    {

        $eventTypeLateThreshold = $this->model();
        $uniqueThresholdMinutes = Rule::unique('event_type_late_thresholds', 'id');

        if ($eventTypeLateThreshold) {
            $uniqueThresholdMinutes->ignoreModel($eventTypeLateThreshold);
        }

        return [
            'event_type_id' => ['nullable', 'exists:event_types,id'],
            'threshold_minutes' => ['required', 'integer', 'min:1', 'max:1440'],
        ];
    }
}

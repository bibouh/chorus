<?php

namespace App\JsonApi\V1\EventTypes;

use Illuminate\Validation\Rule;
use LaravelJsonApi\Laravel\Http\Requests\ResourceRequest;
use LaravelJsonApi\Validation\Rule as JsonApiRule;

class EventTypeRequest extends ResourceRequest
{

    /**
     * Get the validation rules for the resource.
     *
     * @return array
     */
    public function rules(): array
    {
        $eventType = $this->model();
        $eventTypeId = $eventType ? $eventType->getKey() : null;

        return [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('event_types', 'name')->ignore($eventTypeId),
            ],
            'slug' => [
                'required',
                'string',
                'max:100',
                Rule::unique('event_types', 'slug')->ignore($eventTypeId),
            ],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'icon' => ['nullable', 'string', 'max:50'],
        ];
    }

}
